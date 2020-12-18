<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="public">
    <title>Resources Module - PIG Home</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
</head>
<body>
<div class="wrapper">
    <center>
    <table>
        <tr>
        <td style='vertical-align:top;'>
        <div style="text-align:left;">
            <center>
                <table class="titleTable" style="background-image:url('images/resourcestitle.jpg');background-repeat:no-repeat;width:900px;text-align:left;">
                    <tr style='vertical-align:top;'>
                        <td style='height:53px;'>
                        &nbsp;
                        </td>
                    </tr>
                </table>
                <br>
                <div style='text-align:left;'>
                    <table class='borderedFormTable' >
                        <tr style='vertical-align:top;'>
                            <td style="padding-right:10px;">
                                <br>
                                <form method="get" action="pig.php">
                                    <input type="search" title="Search Coral" size="60" id="name" name="name" />
                                    <input type="submit" name="btn_searchName" value="go!" class="searchButton" />
                                    <input type="hidden" name="action" value="getPigSearchResources">
                                    <input type="hidden" name="resourceTypeID" value="1">
                                </form>
                                <br>
                                <a href='pig.php'>Advanced Search</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </center>
        </div>
        </td>
        </tr>
    </table>
    </center>
</div>
</body>
</html>
