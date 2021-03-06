<?php

class RalPalette extends SafeObject
{



	// constants goes here
	public static $RAL_CODES = array (
		1000 => 'ccc188',
		1001 => 'ceb487',
		1002 => 'd0b173',
		1003 => 'f2ad00',
		1004 => 'e4a700',
		1005 => 'c79600',
		1006 => 'd99300',
		1007 => 'e69400',
		1011 => 'd8ba2e',
		1012 => 'af8552',
		1013 => 'e5dfcc',
		1014 => 'dfcea1',
		1015 => 'e6d9bd',
		1016 => 'ecea41',
		1017 => 'f6b256',
		1018 => 'fdda38',
		1019 => 'a6937b',
		1020 => 'a09465',
		1021 => 'f2c000',
		1023 => 'f2bf00',
		1024 => 'b89650',
		1027 => 'a4861a',
		1028 => 'ffa600',
		1032 => 'e2ac00',
		1033 => 'f7a11f',
		1034 => 'eba557',
		2000 => 'd97604',
		2001 => 'bb4926',
		2002 => 'c13524',
		2003 => 'f97a31',
		2004 => 'e8540d',
		2008 => 'f46f29',
		2009 => 'db5316',
		2010 => 'd55d23',
		2011 => 'ea7625',
		2012 => 'd6654e',
		3000 => 'a02725',
		3001 => 'a0001c',
		3002 => '991424',
		3003 => '870a24',
		3004 => '6c1b2a',
		3005 => '581e29',
		3007 => '402226',
		3009 => '6d312b',
		3011 => '791f24',
		3012 => 'c68873',
		3013 => '992a28',
		3014 => 'cf7278',
		3015 => 'e3a0ac',
		3016 => 'ab392d',
		3017 => 'cc515e',
		3018 => 'ca3f51',
		3020 => 'bf111b',
		3022 => 'd36b56',
		3027 => 'b01d42',
		3031 => 'a7323e',
		4001 => '865d86',
		4002 => '8f3f51',
		4003 => 'ca5b91',
		4004 => '69193b',
		4005 => '7e63a1',
		4006 => '912d76',
		4007 => '48233e',
		4008 => '853d7d',
		4009 => '9d8493',
		5000 => '2f4a71',
		5001 => '0e4666',
		5002 => '162e7b',
		5003 => '193058',
		5004 => '1a1d2a',
		5005 => '004389',
		5007 => '38618c',
		5008 => '2d3944',
		5009 => '245878',
		5010 => '00427f',
		5011 => '1a2740',
		5012 => '2781bb',
		5013 => '202e53',
		5014 => '667b9a',
		5015 => '0071b5',
		5017 => '004c91',
		5018 => '138992',
		5019 => '005688',
		5021 => '00747d',
		5022 => '28275a',
		5023 => '486591',
		5024 => '6391b0',
		6000 => '327663',
		6001 => '266d3b',
		6002 => '276230',
		6003 => '4e553d',
		6004 => '004547',
		6005 => '0e4438',
		6006 => '3b3d33',
		6007 => '2b3626',
		6008 => '302f22',
		6009 => '213529',
		6010 => '426e38',
		6011 => '68825f',
		6012 => '293a37',
		6013 => '76785b',
		6014 => '443f31',
		6015 => '383b34',
		6016 => '00664f',
		6017 => '4d8542',
		6018 => '4b9b3e',
		6019 => 'b2d8b4',
		6020 => '394937',
		6021 => '87a180',
		6022 => '3c372a',
		6024 => '008455',
		6025 => '56723d',
		6026 => '005c54',
		6027 => '77bbbd',
		6028 => '2e554b',
		6029 => '006f43',
		6032 => '00855a',
		6033 => '3f8884',
		6034 => '75adb1',
		7000 => '798790',
		7001 => '8c969f',
		7002 => '827d67',
		7003 => '79796c',
		7004 => '999a9f',
		7005 => '6d7270',
		7006 => '766a5d',
		7008 => '756444',
		7009 => '585e55',
		7010 => '565957',
		7011 => '525a60',
		7012 => '575e62',
		7013 => '585346',
		7015 => '4c5057',
		7016 => '363d43',
		7021 => '2e3236',
		7022 => '464644',
		7023 => '7f8279',
		7024 => '484b52',
		7026 => '354044',
		7030 => '919089',
		7031 => '5b686f',
		7032 => 'b5b5a7',
		7033 => '7a8376',
		7034 => '928d75',
		7035 => 'c4caca',
		7036 => '949294',
		7037 => '7e8082',
		7038 => 'b0b3af',
		7039 => '6d6b64',
		7040 => '9aa0a7',
		7042 => '929899',
		7043 => '505455',
		7044 => 'bab9b0',
		8000 => '8b7045',
		8001 => '9c6935',
		8002 => '774c3b',
		8003 => '815333',
		8004 => '904e3b',
		8007 => '6b442a',
		8008 => '735230',
		8011 => '5b3927',
		8012 => '64312a',
		8014 => '49372a',
		8015 => '5a2e2a',
		8016 => '4f3128',
		8017 => '45302b',
		8019 => '3b3332',
		8022 => '1e1a1a',
		8023 => 'a45c32',
		8024 => '7b5741',
		8025 => '765d4d',
		8028 => '4f3b2b',
		9001 => 'eee9da',
		9002 => 'dadbd5',
		9003 => 'f8f9fb',
		9004 => '252427',
		9005 => '151619',
		9010 => 'f4f4ed',
		9011 => '1f2126',
		9016 => 'f3f6f6',
		9017 => '1b191d',
		9018 => 'cbd2d0');

}