<?php
// TODO: extract common body contents from table.titleTable
?>

<body>
<noscript>
    <font face='arial'>
        <?php echo _("JavaScript must be enabled in order for you to use CORAL. However, it seems JavaScript is either disabled or not supported by your browser. To use CORAL, enable JavaScript by changing your browser options, then ");?>
        <a href=""><?php echo _("try again");?></a>.
    </font>
</noscript>
<?php
// TODO: extract inline css to stylesheet and replace deprecated tags with equivalents (e.g. <center> -> <div class="center">)

// TODO: handle module-specific stuff
?>
<div class="wrapper">
    <center>
        <table id="main-table">
            <tr>
                <td style='vertical-align:top;'>
                    <div style="text-align:left;">
                        <center>
                            <table class="titleTable" style="width:1024px;text-align:left;">
                                <tr style='vertical-align:top;'>
                                    <td style='height:53px;' colspan='3'>

                                        <div id="main-title">
                                            <img src="images/title-icon-resources.png" />
                                            <span id="main-title-text">Resources</span>
                                            <span id="powered-by-text">Powered by<img src="images/logo-coral.jpg" /></span>
                                        </div>

                                        <div id="menu-login" style='margin-top:1px;'>
                <span class='smallText' style='color:#526972;'>
                Hello, coral                </span><br />

                                            <a href='http://localhost/Coral/auth/?logout' id='logout' title='logout'>logout</a><span id='divider'> | </span><a href='http://docs.coral-erm.org/' id='help' target='_blank'>Help</a><span id='divider'> | </span>
                                            <span id="setLanguage">
                    <select name="lang" id="lang" class="dropDownLang">
                       <option value='en_US'>English</option><option value='fr_FR'>Français</option><option value='zh_CN'>中文 (简体)</option><option value='zh_TW'>中文 (正體)</option>
                    </select>
                </span>
                                        </div>

                                    </td>
                                </tr>

                                <tr style='vertical-align:top'>
                                    <td style='width:870px;height:19px;' id="main-menu-titles" colspan="2">

                                        <a href='index.php' title="Home">
                                            <div class="main-menu-link active">
                                                <img src="images/menu/icon-home.png" />
                                                <span>Home</span>
                                            </div>
                                        </a>

                                        <a href='ajax_forms.php?action=getNewResourceForm&height=503&width=775&resourceID=&modal=true' class='thickbox' id='newResource' title="New Resource">
                                            <div class="main-menu-link">
                                                <img src="images/menu/icon-plus-square.png" />
                                                <span>New Resource</span>
                                            </div>
                                        </a>

                                        <a href='queue.php' title="My Queue">
                                            <div class="main-menu-link ">
                                                <img src="images/menu/icon-queue.png" />
                                                <span>My Queue</span>
                                            </div>
                                        </a>

                                        <a href='import.php' title="Import">
                                            <div class="main-menu-link ">
                                                <img src="images/menu/icon-import.png" />
                                                <span>File Import</span>
                                            </div>
                                        </a>

                                        <a href='admin.php' title="Admin">
                                            <div class="main-menu-link ">
                                                <img src="images/menu/icon-admin.png" />
                                                <span>Admin</span>
                                            </div>
                                        </a>

                                    </td>

                                    <td style='width:130px;height:19px;' align='right'>

                                        <div style='text-align:left;'>
                                            <ul class="tabs">
                                                <li id="change-mod-menu"><span>Change Module</span><i class="fa fa-chevron-down"></i>
                                                    <ul class="coraldropdown">
                                                        <li class="change-mod-item"><a href="http://localhost/Coral/" target='_blank' title="Main Menu"><img src='images/change/icon-mod-main.png'><span>Main Menu</span></a></li>
                                                        <li class="change-mod-item"><a href="http://localhost/Coral/organizations/" target='_blank' title="Organizations module"><img src='images/change/icon-mod-organizations.png'><span>Organizations</span></a></li>
                                                        <li class="change-mod-item"><a href="http://localhost/Coral/licensing/" target='_blank' title="Licensing module"><img src='images/change/icon-mod-licensing.png'><span>Licensing</span></a></li>
                                                        <li class="change-mod-item"><a href="http://localhost/Coral/usage/" target='_blank' title="Usage Statistics module"><img src='images/change/icon-mod-usage.png'><span>Usage Statistics</span></a></li>
                                                        <li class="change-mod-item"><a href="http://localhost/Coral/management/" target='_blank' title="Management module"><img src='images/change/icon-mod-management.png'><span>Management</span></a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>

                                    </td>
                                </tr>
                            </table>
                            <script>
                                $("#lang").change(function() {
                                    setLanguage($("#lang").val());
                                    location.reload();
                                });

                                function setLanguage(lang) {
                                    var wl = window.location, now = new Date(), time = now.getTime();
                                    var cookievalid=2592000000; // 30 days (1000*60*60*24*30)
                                    time += cookievalid;
                                    now.setTime(time);
                                    document.cookie ='lang='+lang+';path=/'+';domain='+wl.host+';expires='+now;
                                }
                            </script>
                            <span id='span_message' class='darkRedText' style='text-align:left;'></span>
