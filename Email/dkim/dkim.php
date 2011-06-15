<?
/***************************************************************************\
*  PHP-DKIM ($Id: dkim.php,v 1.2 2008/09/30 10:21:52 evyncke Exp $)
*  
*  Copyright (c) 2008 
*  Eric Vyncke
*          
* This program is a free software distributed under GNU/GPL licence.
* See also the file GPL.html
*
* THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR 
* IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
* OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
* IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
* NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
* DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
* THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
*THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***************************************************************************/

require_once 'dkim-cfg.php' ;

if ($open_SSL_pub == '' or $open_SSL_priv == '') {
	die("DKIM not configured, please run:<ol>
	<li>openssl genrsa -out key.priv 384</li>
	<li>openssl rsa -in key.priv -out key.pub -pubout -outform PEM</li>
	</ol> 
	Then copy & paste the public and private keys into dkim-cfg.php") ;
}

function BuildDNSTXTRR() {
	global $open_SSL_pub,$DKIM_s ;
	
	$pub_lines=explode("\n",$open_SSL_pub) ;
	$txt_record="$DKIM_s._domainkey\tIN\tTXT\t\"v=DKIM1\\; k=rsa\\; g=*\\; s=email\; h=sha1\\; t=s\\; p=" ;
	foreach($pub_lines as $pub_line)
		if (strpos($pub_line,'-----') !== 0) $txt_record.=$pub_line ;
	$txt_record.="\;\"" ;
	print("Excellent, you have DKIM keys
	You should add the following DNS RR:
	$txt_record

") ;
}
	
function DKIMQuotedPrintable($txt) {
    $tmp="";
    $line="";
    for ($i=0;$i<strlen($txt);$i++) {
		$ord=ord($txt[$i]) ;
        if ( ((0x21 <= $ord) && ($ord <= 0x3A))
			|| $ord == 0x3C
			|| ((0x3E <= $ord) && ($ord <= 0x7E)) )
            $line.=$txt[$i];
        else
            $line.="=".sprintf("%02X",$ord);
    }
    return $line;
}

function DKIMBlackMagic($s) {
	global $open_SSL_priv ;
	if (openssl_sign($s, $signature, $open_SSL_priv))
		return base64_encode($signature) ;
	else
		die("Cannot sign") ;
}

function NiceDump($what,$body) {
	print("After canonicalization ($what):\n") ;
	for ($i=0; $i<strlen($body); $i++)
		if ($body[$i] == "\r") print("'OD'") ;
		elseif ($body[$i] == "\n") print("'OA'\n") ;
		elseif ($body[$i] == "\t") print("'09'") ;
		elseif ($body[$i] == " ") print("'20'") ;
		else print($body[$i]) ;
	print("\n------\n") ;
}

function SimpleHeaderCanonicalization($s) {
	return $s ;
}

function RelaxedHeaderCanonicalization($s) {
	// First unfold lines
	$s=preg_replace("/\r\n\s+/"," ",$s) ;
	// Explode headers & lowercase the heading
	$lines=explode("\r\n",$s) ;
	foreach ($lines as $key=>$line) {
		list($heading,$value)=explode(":",$line,2) ;
		$heading=strtolower($heading) ;
		$value=preg_replace("/\s+/"," ",$value) ; // Compress useless spaces
		$lines[$key]=$heading.":".trim($value) ; // Don't forget to remove WSP around the value
	}
	// Implode it again
	$s=implode("\r\n",$lines) ;
	// Done :-)
	return $s ;
}

function SimpleBodyCanonicalization($body) {
	if ($body == '') return "\r\n" ;
	
	// Just in case the body comes from Windows, replace all \r\n by the Unix \n
	$body=str_replace("\r\n","\n",$body) ;
	// Replace all \n by \r\n
	$body=str_replace("\n","\r\n",$body) ;
	// Should remove trailing empty lines... I.e. even a trailing \r\n\r\n
	// TODO
	while (substr($body,strlen($body)-4,4) == "\r\n\r\n")
		$body=substr($body,0,strlen($body)-2) ;
//	NiceDump('SimpleBody',$body) ;
	return $body ;
}

function AddDKIM($headers_line,$subject,$body) {
	global $DKIM_s, $DKIM_d, $DKIM_i;
	
//??? a tester	$body=str_replace("\n","\r\n",$body) ;
	$DKIM_a='rsa-sha1'; // Signature & hash algorithms
	$DKIM_c='relaxed/simple'; // Canonicalization of header/body
	$DKIM_q='dns/txt'; // Query method
	$DKIM_t=time() ; // Signature Timestamp = number of seconds since 00:00:00 on January 1, 1970 in the UTC time zone
	$subject_header="Subject: $subject" ;
	$headers=explode("\r\n",$headers_line) ;
	foreach($headers as $header)
		if (strpos($header,'From:') === 0)
			$from_header=$header ;
		elseif (strpos($header,'To:') === 0)
			$to_header=$header ;
	$from=str_replace('|','=7C',DKIMQuotedPrintable($from_header)) ;
	$to=str_replace('|','=7C',DKIMQuotedPrintable($to_header)) ;
	$subject=str_replace('|','=7C',DKIMQuotedPrintable($subject_header)) ; // Copied header fields (dkim-quoted-printable
	$body=SimpleBodyCanonicalization($body) ;
	$DKIM_l=strlen($body) ; // Length of body (in case MTA adds something afterwards)
	$DKIM_bh=base64_encode(pack("H*", sha1($body))) ; // Base64 of packed binary SHA-1 hash of body
	$i_part=($DKIM_i == '')? '' : " i=$DKIM_i;" ;
	$b='' ; // Base64 encoded signature
	$dkim="DKIM-Signature: v=1; a=$DKIM_a; q=$DKIM_q; l=$DKIM_l; s=$DKIM_s;\r\n".
		"\tt=$DKIM_t; c=$DKIM_c;\r\n".
		"\th=From:To:Subject;\r\n".
		"\td=$DKIM_d;$i_part\r\n".
		"\tz=$from\r\n".
		"\t|$to\r\n".
		"\t|$subject;\r\n".
		"\tbh=$DKIM_bh;\r\n".
		"\tb=";
	$to_be_signed=RelaxedHeaderCanonicalization("$from_header\r\n$to_header\r\n$subject_header\r\n$dkim") ;
	$b=DKIMBlackMagic($to_be_signed) ;
	return "X-DKIM: php-dkim.sourceforge.net\r\n".$dkim.$b."\r\n" ;
}
?>