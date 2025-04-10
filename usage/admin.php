<?php
$pageTitle = _('Administration');
include 'templates/header.php';


if ($user->isAdmin()){
?>

<main id="main-content">
  <article>
  <div class="header">
    <h2><?php echo _("Users");?></h2>
    <span id='span_newUser' class='adminAddInput addElement'>
      <button type="button" onclick='myDialog("ajax_forms.php?action=getAdminUserUpdateForm&height=196&width=248&modal=true",200,450)' class='thickbox btn' id='expression'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add user")."' />";?></button>
    </span>
  </div>

  <p id='span_User_response' class='error'></p>
  
  <div id='div_User'>
    <img src = "images/circle.gif"><?php echo _("Loading...");?>
  </div>

  <h3>
    <?php echo _("Email addresses for logs");?>
    <span id='span_newEmailAddress' class='adminAddInput'>
      <button type="button" onclick='myDialog("ajax_forms.php?action=getLogEmailAddressForm&height=122&width=238&modal=true",150,450)' class='thickbox btn'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add mail adress")."' />";?></button>
    </span>
  </h3>
  <p id='span_EmailAddress_response'></p>

  <div id='div_emailAddresses'>
    <img src = "images/circle.gif"><?php echo _("Loading...");?>
  </div>
  <h3 class="headerText"><?php echo _("Line Limits for File Upload/SUSHI Import Confirmations"); ?></h3>
  <p id="span_Limit_response"></p>
  <div id="div_limit">
    <img src="images/circle.gif"><?php echo _("Loading...");?>
  </div>


  <h3 class="headerText"><?php echo _("Outlier Parameters");?></h3>
  <p id='span_Outlier_response'></p>
  <div id='div_outliers'>
    <img src = "images/circle.gif"><?php echo _("Loading...");?>
  </div>

  </article>
</main>

<script type="text/javascript" src="js/admin.js"></script>

<?php
} // if 
include 'templates/footer.php';
?>
</body>
</html>