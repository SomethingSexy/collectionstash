<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title></title>
        <style></style>
    </head>
    <body>
      <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;'>
          <tr>
              <td align="center" valign="top">
                  <table border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer" style="border: 3px #DDDDDD solid; background-color: #F1F1F1;">
                      <tr style="background-color: #2FA4E7; color: #FFF;">
                          <td align="center" valign="top">
                              <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailHeader">
                                  <tr>
                                      <td align="center" valign="top" style="font-size: 40px; padding: 0;">
                                          <img src="http://collectionstash.com/img/icon/add_stash_link_25x25.png"><a style="color: #FFF; text-decoration: none;" href="http://<?php echo env('SERVER_NAME'); ?>">Collection Stash</a>
                                      </td>
                                  </tr>
                              </table>
                          </td>
                      </tr>
                      <tr>
                          <td align="center" valign="top">
                              <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailBody">
                                  <tr>
                                      <td valign="top" style="padding: 0;">
                                         <p style="padding: 0; margin: 0;">Hi there,</p>
                                         <br/>
                                         <p style="padding: 0; margin: 0;"><?php echo $message; ?></p>
                                         <br/>
                                         <?php echo $content_for_layout; ?>
                                      </td>
                                  </tr>
                              </table>
                          </td>
                      </tr>
                      <tr>
                          <td align="center" valign="top" style="border-top: 3px #DDDDDD solid;">
                              <table border="0" cellpadding="5" cellspacing="0" width="100%" id="emailFooter">
                                  <tr>
                                      <td align="left" valign="top">
                                          <p style="padding: 0; margin: 0; font-size: 12px;">You can unsubcribe to these emails by updating your notification settings in your Account Settings.</p>
                                      </td>
                                  </tr>
                              </table>
                          </td>
                      </tr>
                  </table>
              </td>
          </tr>
      </table>
    </body>
</html>