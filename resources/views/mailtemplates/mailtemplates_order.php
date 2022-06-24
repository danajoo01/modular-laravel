<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
  <head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    <meta http-equiv='Content-Type' content='text/css; charset=UTF-8'/>
    <title>Berrybenka Email Notification</title>
    <style media='all'>
      /* Client-specific Styles */
      #outlook a{padding:0;} /* Force Outlook to provide a 'view in browser' button. */
      body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
      body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */
      /* Reset Styles */
      body{margin:0; padding:0;}
      img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
      table td{border-collapse:collapse;}
      #backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}
      /* Template Styles */
      /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: COMMON PAGE ELEMENTS /\/\/\/\/\/\/\/\/\/\ */
      /**
      * @tab Page
      * @section background color
      * @tip Set the background color for your email. You may want to choose one that matches your company's branding.
      * @theme page
      */
      body, #backgroundTable{
        /*@editable*/ background-color:#FAFAFA;
      }
      /**
      * @tab Page
      * @section email border
      * @tip Set the border for your email.
      */
      #templateContainer{
        /*@editable*/ border: 1px solid #888;
      }
      /**
     * @tab Page
     * @section heading 1
     * @tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
     * @style heading 1
     */
      h1, .h1{
        /*@editable*/ color:#202020;
        display:block;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:34px;
        /*@editable*/ font-weight:bold;
        /*@editable*/ line-height:100%;
        margin-top:0;
        margin-right:0;
        margin-bottom:10px;
        margin-left:0;
        /*@editable*/ text-align:left;
      }
      /**
     * @tab Page
     * @section heading 2
     * @tip Set the styling for all second-level headings in your emails.
     * @style heading 2
     */
      h2, .h2{
        /*@editable*/ color:#202020;
        display:block;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:30px;
        /*@editable*/ font-weight:bold;
        /*@editable*/ line-height:100%;
        margin-top:0;
        margin-right:0;
        margin-bottom:10px;
        margin-left:0;
        /*@editable*/ text-align:left;
      }
      /**
      * @tab Page
      * @section heading 3
      * @tip Set the styling for all third-level headings in your emails.
      * @style heading 3
      */
      h3, .h3{
        /*@editable*/ color:#202020;
        display:block;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:26px;
        /*@editable*/ font-weight:bold;
        /*@editable*/ line-height:100%;
        margin-top:0;
        margin-right:0;
        margin-bottom:10px;
        margin-left:0;
        /*@editable*/ text-align:left;
      }
      /**
      * @tab Page
      * @section heading 4
      * @tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
      * @style heading 4
      */
      h4, .h4{
        /*@editable*/ color:#202020;
        display:block;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:22px;
        /*@editable*/ font-weight:bold;
        /*@editable*/ line-height:100%;
        margin-top:0;
        margin-right:0;
        margin-bottom:10px;
        margin-left:0;
        /*@editable*/ text-align:left;
      }
      /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: PREHEADER /\/\/\/\/\/\/\/\/\/\ */
      /**
      * @tab Header
      * @section preheader style
      * @tip Set the background color for your email's preheader area.
      * @theme page
      */
      #templatePreheader{
        /*@editable*/ background-color:#FAFAFA;
      }

      /**
      * @tab Header
      * @section preheader text
      * @tip Set the styling for your email's preheader text. Choose a size and color that is easy to read.
      */
      .preheaderContent div{
        /*@editable*/ color:#505050;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:10px;
        /*@editable*/ line-height:100%;
        /*@editable*/ text-align:left;
      }
      /**
      * @tab Header
      * @section preheader link
      * @tip Set the styling for your email's preheader links. Choose a color that helps them stand out from your text.
      */
      .preheaderContent div a:link, .preheaderContent div a:visited, /* Yahoo! Mail Override */ .preheaderContent div a .yshortcuts /* Yahoo! Mail Override */{
        /*@editable*/ color:#336699;
        /*@editable*/ font-weight:normal;
        /*@editable*/ text-decoration:underline;
      }
      /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: HEADER /\/\/\/\/\/\/\/\/\/\ */
      /**
      * @tab Header
      * @section header style
      * @tip Set the background color and border for your email's header area.
      * @theme header
      */
      #templateHeader{
        /*@editable*/ background-color:#FFFFFF;
        /*@editable*/ border-bottom:0;
      }
      /**
      * @tab Header
      * @section header text
      * @tip Set the styling for your email's header text. Choose a size and color that is easy to read.
      */
      .headerContent{
        /*@editable*/ color:#202020;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:34px;
        /*@editable*/ font-weight:bold;
        /*@editable*/ line-height:100%;
        /*@editable*/ padding:0;
        /*@editable*/ text-align:center;
        /*@editable*/ vertical-align:middle;
      }
      /**
      * @tab Header
      * @section header link
      * @tip Set the styling for your email's header links. Choose a color that helps them stand out from your text.
      */
      .headerContent a:link, .headerContent a:visited, /* Yahoo! Mail Override */ .headerContent a .yshortcuts /* Yahoo! Mail Override */{
        /*@editable*/ color:#336699;
        /*@editable*/ font-weight:normal;
        /*@editable*/ text-decoration:underline;
      }
      #headerImage{
        height:auto;
        max-width:600px !important;
      }
      /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: MAIN BODY /\/\/\/\/\/\/\/\/\/\ */
      /**
      * @tab Body
      * @section body style
      * @tip Set the background color for your email's body area.
      */
      #templateContainer, .bodyContent{
        /*@editable*/ background-color:#FFFFFF;
      }
      /**
      * @tab Body
      * @section body text
      * @tip Set the styling for your email's main content text. Choose a size and color that is easy to read.
      * @theme main
      */
      .bodyContent div{
        /*@editable*/ color:#505050;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:14px;
        /*@editable*/ line-height:150%;
        /*@editable*/ text-align:left;
      }
      .bodyContent div pre {
        /*@editable*/ color:#505050;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:12px;
        /*@editable*/ line-height:150%;
        /*@editable*/ text-align:left;
      }
      /**
      * @tab Body
      * @section body link
      * @tip Set the styling for your email's main content links. Choose a color that helps them stand out from your text.
      */
      .bodyContent div a:link, .bodyContent div a:visited, /* Yahoo! Mail Override */ .bodyContent div a .yshortcuts /* Yahoo! Mail Override */{
        /*@editable*/ color:#336699;
        /*@editable*/ font-weight:normal;
        /*@editable*/ text-decoration:underline;
      }
      .bodyContent img{
        display:inline;
        height:auto;
      }
      /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: FOOTER /\/\/\/\/\/\/\/\/\/\ */
      /**
      * @tab Footer
      * @section footer style
      * @tip Set the background color and top border for your email's footer area.
      * @theme footer
      */
      #templateFooter{
        /*@editable*/ background-color:#FFFFFF;
        /*@editable*/ border-top:0;
      }
      /**
      * @tab Footer
      * @section footer text
      * @tip Set the styling for your email's footer text. Choose a size and color that is easy to read.
      * @theme footer
      */
      .footerContent div{
        /*@editable*/ color:#707070;
        /*@editable*/ font-family:Arial;
        /*@editable*/ font-size:12px;
        /*@editable*/ line-height:125%;
        /*@editable*/ text-align:left;
      }
      /**
      * @tab Footer
      * @section footer link
      * @tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
      */
      .footerContent div a:link, .footerContent div a:visited, /* Yahoo! Mail Override */ .footerContent div a .yshortcuts /* Yahoo! Mail Override */{
        /*@editable*/ color:#336699;
        /*@editable*/ font-weight:normal;
        /*@editable*/ text-decoration:underline;
      }
      .footerContent img{
        display:inline;
      }
      /**
      * @tab Footer
      * @section social bar style
      * @tip Set the background color and border for your email's footer social bar.
      * @theme footer
      */
      #social {
        /*@editable*/ background-color:#666666;
        /*@editable*/ border:0;
      }
      /**
      * @tab Footer
      * @section social bar style
      * @tip Set the background color and border for your email's footer social bar.
      */
      #social div  {
        /*@editable*/ text-align:center;
      }
      /**
      * @tab Footer
      * @section utility bar style
      * @tip Set the background color and border for your email's footer utility bar.
      * @theme footer
      */
      #utility{
        /*@editable*/ background-color:#FFFFFF;
        /*@editable*/ border:0;
      }
      /**
      * @tab Footer
      * @section utility bar style
      * @tip Set the background color and border for your email's footer utility bar.
      */
      #utility div{
        /*@editable*/ text-align:center;
      }
      #monkeyRewards img{
        max-width:190px;
      }
      .thx-wrapper{background:#f2f2f2;display:inline-block;padding:20px 30px;border:1px solid #ccc;border-radius:3px;text-align:center;color:#555;}
      .logo-bca-thx{width:100px;display:block;margin:0 auto 10px auto;}
      .thx-wrapper p{margin:0;font-size:14px;letter-spacing:1px;letter-spacing:1.5;color:#999;margin-bottom:3px;}
      .thx-wrapper h1{font-size:16px;letter-spacing:1px;margin:0;color:#666;}
      a.btn-kredivo {background: #666 none repeat scroll 0 0;color: #fff !important;display: inline-block;letter-spacing: 2px;padding: 15px 61px;text-transform: uppercase;margin:0 auto;text-decoration: none !important;border-radius: 2px;}
    </style>
  </head>
  <body leftmargin='0' marginwidth='0' topmargin='0' marginheight='0' offset='0'>
    <center>
      <table border='0' cellpadding='0' cellspacing='0' height='100%' width='100%' id='backgroundTable'>
        <tr>
          <td align='center' valign='top'>
            <table border='0' cellpadding='10' cellspacing='0' width='700' id='templatePreheader'>
              <tr>
                <td valign='top' class='preheaderContent'></td>
              </tr>
            </table>
            <table border='0' cellpadding='0' cellspacing='0' width='700' id='templateContainer' style='border:1px solid #999;'>
              <tr>
                <td align='center' valign='top'>
                  <table border='0' cellpadding='0' cellspacing='0' width='700' id='templateBody'>
                    <tr>
                      <td valign='top' class='bodyContent'>
                        <table border='0' cellpadding='15' cellspacing='0' width='100%'>
                          <tr>
                            <td valign='top'>
                              <div mc:edit='std_content00'>
                                <?php echo $mail_message ?>
                              </div>
                            </td>
                          </tr>
                        </table>
                        <table border='0' cellpadding='0' cellspacing='0' width='700' style='font-family:Arial, sans-serif; font-size:14px;'>
                          <tr style='background-color:#777777; color:#fff;'>
                            <td width='300' style='padding:10px 15px; font-weight:bold; border-right:1px solid #fff;'>
                              PRODUK
                            </td>
                            <td width='140' style='padding:10px 15px; font-weight:bold; border-right:1px solid #fff; text-align:center;'>
                              HARGA
                            </td>
                            <td width='100' style='padding:10px 15px; font-weight:bold; border-right:1px solid #fff; text-align:center;'>
                              JUMLAH
                            </td>
                            <td width='140' style='padding:10px 15px; font-weight:bold; text-align:center;'>
                              TOTAL
                            </td>
                          </tr>
                          <?php echo $mail_message_product ?>
                          <?php echo $mail_message_value ?>
                        </table>
                        <table border='0' cellpadding='0' cellspacing='0' width='700' style='font-family:Arial,sans-serif;font-size:14px;'>
                          <tr style='background-color:#777777;color:#fff;'>
                            <td colspan='4' style='padding:10px 15px;font-weight:bold;'>
                              INFORMASI PENGIRIMAN
                            </td>
                          </tr>
                          <!--tr>
                            <td colspan='4' style='padding:10px 15px;color:blue;font-size:12px;'>
                              BERKENAAN DENGAN HARI RAYA IDUL FITRI, PENGIRIMAN PRODUK BERRYBENKA DIPERKIRAKAN MENGALAMI KETERLAMBATAN 4-5 HARI UNTUK WILAYAH JABODETABEK DAN 6-10 HARI UNTUK LUAR JABODETABEK. <br/><br/>MOHON MAAF ATAS KETIDAKNYAMANANNYA. <br/><br/>TERIMA KASIH TELAH BERBELANJA DI BERRYBENKA
                            </td>
                          </tr-->
                          <?php echo $mail_message_address ?>                    
                        </table>
                        <table border='0' cellpadding='0' cellspacing='0' width='700' style='font-family:Arial, sans-serif; font-size:14px;'>
                          <tr style='background-color:#777777; color:#fff;'>
                            <td colspan='4' style='padding:10px  15px; font-weight:bold;'>
                              INFORMASI PEMBAYARAN
                            </td>
                          </tr>
                          <tr style='color:#444; line-height:18px;'>
                            <td colspan='4' style='padding:15px ;'>
                              <?php echo $mail_message_payment ?>
                              <?php echo $mail_message_CS ?>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td align='center' valign='top'>
                  <table border='0' cellpadding='0' cellspacing='0' width='700' id='templateFooter'>
                    <tr>
                      <td valign='top' class='footerContent'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='color:#505050'>
                          <tr>
                            <?php echo $mail_message_SM ?>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            <table border='0' cellpadding='10' cellspacing='0' width='700' id='templatePreheader'>
              <tr>
                <td valign='top' class='preheaderContent'>
                  <div mc:edit='std_preheader_content' style='font-size:10px;'>
                    <?php echo $mail_message_footer ?>
                  </div>
                </td>
              </tr>
            </table>
            <br />
          </td>
        </tr> 
      </table>
    </center>
  </body>
</html>