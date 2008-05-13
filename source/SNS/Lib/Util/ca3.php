<?php
// $Header$
// show archive calendar of lilina digest

include 'Calendar.php';

// always modified now
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
// Expires 10 hours later
header("Expires: " .gmdate ("D, d M Y H:i:s", time() + 36000). " GMT");

$today = date('j');
$this_month = date('m');
$this_year = date('Y');
$show_year = $this_year;

/**
 * parse parameters from $_SERVER["PATH_INFO"]: show source or specified year
 */
if ( isset($_SERVER["PATH_INFO"]) && trim($_SERVER["PATH_INFO"]) != "") {
    list($nothing, $year) = explode('/', $_SERVER["PATH_INFO"]);
    if ($year >= 2005 && $year <= $this_year) {
         $show_year = intval($year);
    }
    else if ($year == "source") {
        show_source($_SERVER["SCRIPT_FILENAME"]);
        exit;
    }
}
?>

<html>
<head>
  <meta content="text/html; charset=utf-8" http-equiv="content-type">
  <title><?=$show_year?> Digest by CheDong.com</title>
  <link rel="stylesheet" type="text/css" href="/style.css" media="screen">
<base target="_self" /></head>
<body>

<?php
// customize the date link;
class MyCalendar extends Calendar {
    function getDateLink($day, $month, $year) {
        global $today;
        global $this_month;
        global $this_year;
        $link = "";
        //make link since 2005/05/03 to yesterday
        if ( mktime(0, 0, 0, $month, $day, $year) < (time() - 86400)
             &&  mktime(0, 0, 0, $month, $day, $year) >  mktime(0, 0, 0, 5, 2, 2005) ) {
            $link = "/digest/" . date("Ymd", mktime(0, 0, 0, $month, $day, $year)) . ".html";
        }

        return $link;
    }
}

$cal = new MyCalendar;

// First, create an array of month names, January through December
$chinese_months = array("一月", "二月", "三月", "四月",
                      "五月", "六月", "七月", "八月", "九月",
                      "十月", "十一月", "十二月");

// Then an array of day names, starting with Sunday
$chinese_days = array ("日", "一", "二", "三", "四", "五", "六");

$cal->setMonthNames($chinese_months);
$cal->setDayNames($chinese_days);
$cal->setStartDay(1);

echo "<div class=\"item\">";
echo $cal->getCurrentMonthView();
echo "</div>";
?>
</body>