<?php
 include 'db.php';
 $model = new Db();
 $turistCity = $model->getTouristCity();
 $visitingPlace = $model->getVisitingPlaces();
 $searchdata = $model->getVisitinPlaceData($_POST['city'], $_POST['place'], $_POST['keyword']);
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    </head>
    <body>
        <div style="width: 50%; margin: 0 auto;">
        <div class="hidden-sm bg-info" style="padding: 10px;"> 
        <form action="" method="post" > 
            <div class="col-sm-3"> 
                <select name="city" class="form-control">
                <option value="0">Select City</option>
                <?php foreach($turistCity as $location) {
                    $checked = ($_POST['city'] == $location[id])? 'selected' : '';
                    echo '<option value="'.$location[id].'" '.$checked.'>'.$location[location].'</option>';
                }
                ?>
                </select>
            </div>
            <div class="col-sm-3"> 
                <select name="place" class="form-control">
                    <option value="0">Select Visiting Place</option>
                    <?php foreach($visitingPlace as $place) { 
                        $checked1 = ($_POST['place'] == $place[vid])? 'selected' : '';
                        echo '<option value="'.$place[vid].'"  '.$checked1.'>'.$place[visiting_place].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-3">
                <input type="text" name="keyword" placeholder="Keword" value="<?php echo $_POST['keyword']; ?>"  class="form-control" /> 
            </div>
            <button name="search" class="btn btn-primary">Search</button>
        </form>
        </div>
        <div class="hidden-md bg-warning" style="padding: 10px;">
            <table cellpadding="10" cellspacing="10" class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>City</th>
                    <th>Place</th>
                    <th>History</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                if(count($searchdata) > 0 ){
                foreach($searchdata as $places) {
                    echo '<tr>';
                        echo '<th>'.$i.'</th>';
                        echo '<td>'.$places[location].'</td>';
                        echo '<td>'.$places[visiting_place].'</td>';
                        echo '<td>'.$places[history].'</td>';
                    echo '</tr>';
                    $i++;
                }
                }
                else {
                    echo '<td colspan="4">No Search Result Found.</td>';
                }
                ?>
            </table>
        </div>
        </div>
    </body>
</html>