<!-- hide JavaScript from non-JavaScript browsers

	//  WP Copysafe PDF - Version 3.0.5.1
	//  Copyright (c) 1998-2013 ArtistScope. All Rights Reserved.
	//  www.artistscope.com
	//
	// The Copysafe PDF Reader plugin is supported across all Windows since XP
	//
	// Special JS version for Wordpress

// Debugging outputs the generated html into a textbox instead of rendering
//	option has been moved to wp-copysafe-pdf.php

// REDIRECTS

var m_szLocation = document.location.href.replace(/&/g,'%26');	

var m_szDownloadNo = wpcsp_plugin_url + "download_no.php";
var m_szDownload = wpcsp_plugin_url + "download.php?ref=" + m_szLocation;
var m_szDownloadIE = m_szDownloadFX = m_szDownload ;
//====================================================
//   Current version == 3.0.5.1
//====================================================

var m_nV1=3;
var m_nV2=0;
var m_nV3=5;
var m_nV4=1;

//===========================
//   DO NOT EDIT BELOW 
//===========================

var m_szAgent = navigator.userAgent.toLowerCase();
var m_szBrowserName = navigator.appName.toLowerCase();
var m_szPlatform = navigator.platform.toLowerCase();
var m_bNetscape = false;
var m_bMicrosoft = false;
var m_szPlugin = "";

var m_bWin64 = ((m_szPlatform == "win64") || (m_szPlatform.indexOf("win64")!=-1) || (m_szAgent.indexOf("win64")!=-1));
var m_bWin32 = ((m_szPlatform == "win32") || (m_szPlatform.indexOf("win32")!=-1));
var m_bWin2k = ((m_szAgent.indexOf("windows nt 5.0")!=-1) || (m_szAgent.indexOf("windows 2000")!=-1));
var m_bWinxp = ((m_szAgent.indexOf("windows nt 5.1")!=-1) || (m_szAgent.indexOf("windows xp")!=-1));
var m_bWin2k3 = (m_szAgent.indexOf("windows nt 5.2")!=-1);	
var m_bVista = (m_szAgent.indexOf("windows nt 6.0")!=-1);
var m_bWindows7 = (m_szAgent.indexOf("windows nt 6.1")!=-1);
var m_bWindows8 = (m_szAgent.indexOf("windows nt 6.2")!=-1);
var m_bWindows = (((m_bWin2k) || (m_bWinxp) || (m_bWin2k3) || (m_bVista) || (m_bWindows7) || (m_bWindows8)) && ((m_bWin32) || (m_bWin64)));

var m_bOpera = ((m_szAgent.indexOf("opera")!=-1) && !!(window.opera && window.opera.version) && (m_bpOpera));
var m_bFx3 = ((m_szAgent.indexOf("firefox/3.")!=-1) && (m_szAgent.indexOf("flock")==-1) && (m_szAgent.indexOf("navigator")==-1));
var m_bFx4 = ((m_szAgent.indexOf("firefox/4.")!=-1) && (m_szAgent.indexOf("flock")==-1) && (m_szAgent.indexOf("navigator")==-1));
var m_bFirefox = ((m_szAgent.indexOf("firefox")!=-1) && testCSS("MozBoxSizing") && (!(m_bFx3)) && (!(m_bFx4)) && (m_bpFx));
var m_bSafari = ((m_szAgent.indexOf("safari")!=-1) && Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0 && (m_bpSafari));
var m_bChrome = ((m_szAgent.indexOf("chrome")!=-1) && !!(window.chrome && chrome.webstore && chrome.webstore.install) && (m_bpChrome));
var m_bNav = ((m_szAgent.indexOf("navigator")!=-1) && (m_bpNav));

var m_bNetscape = ((m_bChrome) || (m_bFirefox) || (m_bNav) || (m_bOpera) || (m_bSafari));
var m_bMicrosoft = ((m_szAgent.indexOf("msie")!=-1) && (/*@cc_on!@*/false || testCSS("msTransform")) && (m_bpMSIE)); 

function testCSS(prop) {
    return prop in document.documentElement.style;
}

if (m_bpDebugging == true)
	{
//	document.write("UserAgent= " + m_szAgent + "<br>");
//	document.write("Browser= " + m_szBrowserName + "<br>");
//	document.write("Platform= " + m_szPlatform + "<br>");
//	document.write("Referer= " + m_szLocation + "<br>");
    }

function CopysafePDFVersionCheck()
	{
		var v = typeof document.getElementById != "undefined" && typeof document.getElementsByTagName != "undefined" && typeof document.createElement != "undefined";
		var AC = [0,0,0];
		var x = "";
		
        if (typeof navigator.plugins != "undefined" && navigator.plugins.length > 0)
        {
	        // Navigator, firefox, mozilla

		navigator.plugins.refresh(false);

		var szDescription = "CopySafe PDF Reader";
		var szVersionMatch = "Reader v";

		if (typeof navigator.plugins[szDescription] == "object")
	        {
	            x = navigator.plugins[szDescription].description;
	            ix = x.indexOf(szVersionMatch);
	            if (ix > -1)
	            	x = x.slice(ix + szVersionMatch.length);
	            else
	            	x = "";
	        }
		}
		else if (typeof window.ActiveXObject != "undefined")
		{
			// Internet explorer

			var y = null;

			try
			{
				y = new ActiveXObject("ARTISTSCOPE.PDFReaderWebCtrl")
                x = y.GetVersion();
			}
			catch(t)
			{
			}
		}

		if (x.length > 0)
		{
           	ix1 = x.indexOf(".");
           	ix2 = x.indexOf(".", ix1 + 1);
	            	
           	if (ix1 != -1 && ix2 != -1)
           	{
            	AC[0] = parseInt(x.slice(0, ix1));
            	AC[1] = parseInt(x.slice(ix1 + 1, ix2));
            	AC[2] = parseInt(x.slice(ix2 + 1));
           	}
		}

        return AC;
	}

var arVersion = CopysafePDFVersionCheck();
var szNumeric = "" + arVersion[0] + "." + arVersion[1] + "." + arVersion[2];
	

if ((m_bWindows) && (m_bMicrosoft))
	{
	m_szPlugin = "OCX";
	if ((arVersion[0] < m_nV1) || (arVersion[0] == m_nV1 && arVersion[1] < m_nV2) || (arVersion[0] == m_nV1 && arVersion[1] == m_nV2 && arVersion[2] < m_nV3))
		{
		window.location=unescape(m_szDownloadIE);
		document.MM_returnValue=false;
		}
	}
else if ((m_bWindows) && (m_bNetscape))
	{
	m_szPlugin = "DLL";
	if ((arVersion[0] < m_nV1) || (arVersion[0] == m_nV1 && arVersion[1] < m_nV2) || (arVersion[0] == m_nV1 && arVersion[1] == m_nV2 && arVersion[2] < m_nV3))
		{
		window.location=unescape(m_szDownloadFX);
		document.MM_returnValue=false;
		}
	}
else 
	{
	window.location=unescape(m_szDownloadNo);
	document.MM_returnValue=false;
	}


// The copysafe-insert functions

function insertCopysafePDF(szDocName)
{
    if (m_bpDebugging == true)
        { 
        document.writeln("<textarea rows='27' cols='80'>"); 
        }       
    if ((m_szPlugin == "DLL"))
    {
    	szObjectInsert = "type='application/x-artistscope-pdfreader5' codebase='" + wpcsp_plugin_url +"download.asp' ";
    	document.writeln("<ob" + "ject " + szObjectInsert + " width='" + m_bpWidth + "' height='" + m_bpHeight + "'>");
    }
    else if (m_szPlugin == "OCX")
    {
        szObjectInsert = "classid='CLSID:DEC3C469-DD45-4C0C-8328-4C48507D9B25'";
        document.writeln("<ob" + "ject id='csviewer' " + szObjectInsert + " width='" + m_bpWidth + "' height='" + m_bpHeight + "'>");
	}
    document.writeln("<param name='Document' value='" + m_szImageFolder + m_szClassName + "' />");
    document.writeln("<param name='PrintsAllowed' value='" + m_bpPrintsAllowed + "' />");
    document.writeln("<param name='PrintAnywhere' value='" + m_bpPrintAnywhere + "' />");
    document.writeln("<param name='AllowCapture' value='" + m_bpAllowCapture + "' />");
    document.writeln("<param name='AllowRemote' value='" + m_bpAllowRemote + "' />"); 
    document.writeln("<param name='Language' value='" + m_bpLanguage + "' />");  
    document.writeln("<param name='Background' value='" + m_bpBackground + "' />");
	document.writeln("</object />");
	
    if (m_bpDebugging == true)
        { document.writeln("</textarea />"); }
}

// -->
