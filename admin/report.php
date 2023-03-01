<?php
include_once 'layouts/header.php';
include_once 'controller/categories_controller.php';
include_once 'controller/report_controller.php';
include_once 'controller/product_controller.php';
include_once 'controller/order_controller.php';

$report = new ReportController();
$item = $report->getItem();
//var_dump($item);
//$product = $report->getTotalItem();
//$daily = $report->getOrderDates();
//$month = $report->getMonths();
 //var_dump($daily);
//$all=$report->getAllResult();
 //var_dump($all);

//var_dump($daily);
$categories = new CategoriesController();
$results = $categories->getCategories();

$parents = array_filter($results, function ($value) {
    if ($value["parent"] == 0)
        return $value;
});


//   echo "<h4>The Best Selling Item:$product</h4>";


$products_controller = new ProductController();
$products = $products_controller->getProducts();

$orders = new OrderController();
$orders = $orders->getOrderinfo();

date_default_timezone_set("Asia/Yangon");
$date_now = date('Y-m-d');
// $month = date("F", strtotime($date_now));

// var_dump($month);
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}

 $daily = $report->getPage($page);
//var_dump($daily);

if (isset($_POST['filter'])) {

    $item = array_values(array_filter($item, function ($value) {;
       
        if((!empty($_POST['start_date']) && !empty($_POST['end_date']))){
            $search_date =  date('Y-m-d', strtotime($value['created_date']));
            $start_date = date('Y-m-d', strtotime($_POST['start_date']));
            $end_date = date('Y-m-d', strtotime($_POST['end_date']));

            if($search_date >= $start_date && $search_date <= $end_date){
                return $value;
            }
        }

        // if((empty($_POST['month']) && empty($_POST['cat_filter'])) && (!empty($_POST['start_date']) && !empty($_POST['end_date']))){
        //     $search_date =  date('Y-m-d', strtotime($value['created_date']));
        //     $start_date = date('Y-m-d', strtotime($_POST['start_date']));
        //     $end_date = date('Y-m-d', strtotime($_POST['end_date']));

        //     if($search_date >= $start_date && $search_date <= $end_date){
        //         return $value;
        //     }
        // }

        // if((!empty($_POST['month']) && empty($_POST['cat_filter'])) && (empty($_POST['start_date']) && empty($_POST['end_date']))){
        //     $search_month = $value['month'];
        //     if($search_month == $_POST['month']){
        //         return $value;
        //     }
        // }

        if(( !empty($_POST['cat_filter'])) && (empty($_POST['start_date']) && empty($_POST['end_date']))){
            $search_cate =  $value['category_id'];   
            if($search_cate == $_POST['cat_filter']){
                return $value;
            }
        }

       
         
    }));
}
?>
<!-- convert php array to json array for javascript -->



<div class="container">
    <h2>Report</h2>
    <form action="" method="post">
        <div class="row my-3">
            <!-- <div class="col-md-2">
                <select name="month" class="form-select" id="month">
                    <option value="0" selected hidden>Choose Month</option>
                    <?php
                    foreach (range(1, 12) as $month) {
                        // echo date('F',mktime(0,0,0,$month,10))."</br>";
                        $monthname = date('F', mktime(0, 0, 0, $month, 10));
                        $monthval = substr(date('F', mktime(0, 0, 0, $month, 10)), 0, 3); //Jan ,Feb
                        echo "<option value='" . $monthname . "'>" . $monthname . "</option>";
                    }
                    ?>

                </select>
            </div> -->
            <div class="col-md-2">
                <select name="cat_filter" class="form-select">
                    <?php
                    echo "<option hidden>Categories Type</option>";
                    for ($i = 0; $i < count($parents); $i++) {
                        echo "<option value='" . $parents[$i]['id'] . "'>" . $parents[$i]['name'] . "</option>";
                    }

                    ?>
                </select>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <input type="date" name="start_date" id="start_date" class="form-control" placeholder="Start Date">

                    </div>
                    <div class="col-md-6">
                        <input type="date" name="end_date" id="end_date" class="form-control" placeholder="End Date">

                    </div>
                </div>
            </div>

            <div class="col-md-2">

                <button id="filter" class="btn btn-sm btn-info" name="filter">စီစစ်မည်</button>
                <button id="filter" class="btn btn-sm btn-danger">ပြန်စမည်</button>

            </div>
    </form>
</div>

<div class="row mt-3">


    <div class="col-md">

        <table class="table table-striped table-bordered" id="order_table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Order Date</th>
                    <th>Item Name</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total Price</th>


                </tr>
            </thead>
            <tbody id="item_table">

                <?php
                $item_page=5;
                $number=1+($page-1)*$item_page;
                $total_price = 0;
                $total_qty = 0;

                for ($i = 0; $i < count($item); $i++) {
                    $total_price += $item[$i]['price'] * $item[$i]['total_qty'];
                    $total_qty += $item[$i]['total_qty'];


                    $id = $i + 1;
                    if (isset($_GET['page'])) {
                        $id = ($_GET['page'] - 1) + $item_page + $i;
                    }

                    echo "<tr><td>" . $id . "</td><td>" . $item[$i]['created_date'] . "</td>
                           <td>" . $item[$i]['name'] . "</td>
                           <td>" . $item[$i]['total_qty'] . "</td>
                           <td>" . $item[$i]['price'] . "</td>
                           <td>" . $item[$i]['price'] * $item[$i]['total_qty'] . "</td>
                           </tr>";
                }

                echo "<tr>";
                echo "<td colspan='3'></td>";
                echo "<td>Total</td>";
                echo "<td>" . $total_qty . "</td>";
                echo "<td>" . $total_price . "</td>";

                echo "</tr>";



                //   for ($i=0; $i <count($product) ; $i++) { 
                //     echo "<tr><td>".$product[$i]['created_date']."</td>
                //             <td>".$product[$i]['total_qty']."</td>
                //             </tr>";

                //   }

                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-10">
        <ul class="pagination">

            <?php
            $pages = ceil(count($item) / $item_page);
            $previous = $page - 1;
            $next = $page + 1;
            if ($page <= 1) {
                echo ' <li class="page-item disabled">
                                         <a class="page-link">Previous</a>
                                        </li>';
            } else {
                if ($previous == 1) {
                    echo ' <li class="page-item ">
                                         <a class="page-link" href="report.php">Previous</a>
                                        </li>';
                }
            }
            if ($page == 1) {
                echo ' <li class="page-item active"><a class="page-link" href="report.php">1</a></li>';
            } else {
                echo ' <li class="page-item"><a class="page-link" href="report.php">1</a></li>';
            }
            for ($index = 2; $index <= $pages; $index++) {
                if ($page == $index) {
                    echo ' <li class="page-item active"><a class="page-link" href="report.php?page=' . $index . '">' . $index . '</a></li>';
                } else {
                    echo ' <li class="page-item "><a class="page-link" href="report.php?page=' . $index . '">' . $index . '</a></li>';
                }
            }
            if ($page >= $pages) {
                echo '<li class="page-item disabled">
                     <a class="page-link" href="#">Next</a>
                     </li>';
            } else {
                echo '<li class="page-item">
                     <a class="page-link" href="report.php?page=' . $next . '">Next</a>
                    </li>';
            }
            ?>



        </ul>
    </div>
</div>




</div>
</div>
<?php
include_once 'layouts/footer.php';


?>