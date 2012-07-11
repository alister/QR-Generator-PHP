<?php
/*
#  This program outputs a QRcode as either a PNG, JPEG, or GIF.
#
#	Original version (C)2000-2009,Y.Swetake (version 0.50i)
#	From: http://www.venus.dti.ne.jp/~swe/program/qr_img0.50i.tar.gz
#	http://www.swetake.com/qr/
#	Licenced as "revised BSD License" by the original author
#
#		Subsequent Changes
#	Copyright (c) 2012, Terence Eden
#	All rights reserved.
#
#	Redistribution and use in source and binary forms, with or without
#	modification, are permitted provided that the following conditions are met:
#		* Redistributions of source code must retain the above copyright
#		  notices, this list of conditions and the following disclaimer.
#		* Redistributions in binary form must reproduce the above copyright
#		  notices, this list of conditions and the following disclaimer in the
#		  documentation and/or other materials provided with the distribution.
#		* Neither the name of the software nor the
#		  names of its contributors may be used to endorse or promote products
#		  derived from this software without specific prior written permission.
#
#	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
#	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
#	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
#	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER(S) BE LIABLE FOR ANY
#	DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
#	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
#	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
#	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
#	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
#	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
#
#
#  This program outputs a QRcode as either a PNG, JPEG, or GIF.
#
#  Supports QRcode version 1-40.
#
#  Requires PHP4.1 and gd 1.6 or higher.
#
#
# [usage]
#   qr.php?d=[data]&e=[(L,M,Q,H)]&s=[int]&v=[(1-40)]&t=[(J,G)]&size=[int]&download=[filename]
#			 (&m=[(1-16)]&n=[(2-16)](&p=[(0-255)],&o=[data]))
#
#   d		= data				URL encoded data.
#   e		= ECC level			L or M or Q or H   (default M)
#   s		= module size		(dafault PNG:4 JPEG:8)
#   v		= version			1-40 or Auto select if you do not set.
#   t		= image type		J: jpeg image, G: GIF image, default: PNG image
#   size	= image size		Integer. Specifies the width & height of the image. Default 400. Max 1480.
#   download = File name	URL encoded filename. If set, the content disposition will change to tell the browser to download the file.
#   raw  = rawurldecode the data. If set, the data in d will be URL decoded changing "%20" into " " etc.
#
#  structured append  m of n (experimental)
#   n= structure append n (2-16)
#   m= structure append m (1-16)
#   p= parity
#   o= original data (URL encoded data)  for calculating parity
#
#	The QR code standard is trademarked by Denso Wave, Inc.
#	http://www.denso-wave.com/qrcode/index-e.html
*/

if ('cli' == PHP_SAPI) {
    error_reporting(-1);

    // fake some demo code
    $_GET['d'] ='hello this is a simple QR with a bit of text. XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $_GET['e'] = 'Q';
    $_GET['t'] = 'J';
    $_GET['size'] = 500;
    $_GET['download'] = 'test';
    $_GET["s"] = null;
    $_GET["v"] = null;
    $_GET["raw"] = null;
}

//	Get the parameters
$qrcode_error_correct= strtolower($_GET["e"]);
$qrcode_module_size  = $_GET["s"];
$qrcode_version      = $_GET["v"];
$qrcode_raw          = $_GET["raw"];
$qrcode_image_type   = strtolower($_GET["t"]);
$qrcode_image_size   = $_GET["size"];
$qrcode_download     = $_GET["download"];

if ($qrcode_raw) {
    $qrcode_data_string  = rawurldecode($_GET["d"]);
} else {
    $qrcode_data_string  = $_GET["d"];
}

//	Experimental Parameters
$qrcode_structureappend_n            = @$_GET["n"];
$qrcode_structureappend_m            = @$_GET["m"];
$qrcode_structureappend_parity       = @$_GET["p"];
$qrcode_structureappend_originaldata = @$_GET["o"];

//	Set the Image Type
//	&t=
if ($qrcode_image_type == "j") {
    $qrcode_image_type = "jpeg";
} elseif ($qrcode_image_type == "g") {
    $qrcode_image_type = "gif";
} else {
    $qrcode_image_type = "png";
}

//	Set the Module Size (this is *not* the same as the Image Size)
//	&s=
if ($qrcode_module_size > 0) {
} else {
    if ($qrcode_image_type == "jpeg") {
        $qrcode_module_size = 8;
    } else {
        $qrcode_module_size = 4;
    }
}

//	Set the Image Size (QR codes are square - so the height and width are the same)
//	&size=
if ($qrcode_image_size == null) {
    $qrcode_image_size = 400;
}

//	Maximum Image Size
if ($qrcode_image_size > 1480) {
  //	If you would prefer to display and error message, uncomment the following line
  //trigger_error("QRcode : Image width must be less than 1480 pixels",E_USER_ERROR);
  $qrcode_image_size = 1480;
}

//	If no data has been supplied
if (strlen($qrcode_data_string) <= 0) {
    trigger_error("QRcode : No data supplied.", E_USER_ERROR);
    exit;
}

//	Create the QR Code
require_once 'QR-Generator/QR.php';
$qr = new QR;
$base_image = $qr->createQR(
    $qrcode_data_string,
    $qrcode_error_correct,
    $qrcode_version,
    $qrcode_image_size,
    $qrcode_structureappend_n,
    $qrcode_structureappend_m,
    $qrcode_structureappend_parity,
    $qrcode_structureappend_originaldata
);

$qr->outputImage($base_image, $qrcode_image_type, $qrcode_download, $qrcode_image_size);

//  All done!
//  Clean up after ourselves!
imagedestroy($base_image);
