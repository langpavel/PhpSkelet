<?
/***************************************************************************\
*  DKIM-TEST ($Id: dkim-test.php,v 1.2 2008/09/30 10:21:52 evyncke Exp $)
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
// DKIM example and test

// Basic configuration of the test programes

$to='dkim-test@testing.dkim.org, check-auth@verifier.port25.com, test@dkimtest.jason.long.name' ;
$sender='john@example.com' ;
$subject='Test of PHP-DKIM' ;
$body="<h1>Congratulations</h1>
You have installed and configured PHP-DKIM correctly!" ;

// Nothing to configure below

require 'dkim.php' ;

BuildDNSTXTRR() ;

$headers="From: \"Fresh DKIM Manager\" <$sender>\r\n".
	"To: $to\r\n".
	"Reply-To: $sender\r\n".
	"Content-Type: text/html\r\n".
	"MIME-Version: 1.0" ;
$headers = AddDKIM($headers,$subject,$body) . $headers;

// Some Unix MTA has a bug and add redundant \r breaking some DKIM implementation
// Based on your configuration, you may want to comment the next line
$headers=str_replace("\r\n","\n",$headers) ; 

$result=mail($to,$subject,$body,$headers,"-f $sender") ;
if (!$result)
	die("Cannot send email to $to") ;

?>