<form  action=" " method="POST"
           enctype="multipart/form-data">
 
  <input type="file"  name="file" >
  
 <input type= "submit" value ="Upload" >
  
</form>

<?php 
 
$servername = "localhost";
$username = "cheminve_ali";
$password = "t00532799?";
$dbname = "cheminve_main";
 
// Create connection
 
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} 
 
echo "connected";
 
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
 
// Include Spout library 
require 'Spout/Autoloader/autoload.php';
 
// check file name is not empty
if (!empty($_FILES['file']['name'])) {
      
    // Get File extension eg. 'xlsx' to check file is excel sheet
    $pathinfo = pathinfo($_FILES["file"]["name"]);
     
    // check file has extension xlsx, xls and also check 
    // file is not empty
   if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') 
           && $_FILES['file']['size'] > 0 ) {
         
        // Temporary file name
        $inputFileName = $_FILES['file']['tmp_name']; 
    
        // Read excel file by using ReadFactory object.
        $reader = ReaderFactory::create(Type::XLSX);
 
        // Open file
        $reader->open($inputFileName);
        $count = 1;
 
        // Number of sheet in excel file
        foreach ($reader->getSheetIterator() as $sheet) {
             
            // Number of Rows in Excel sheet
            foreach ($sheet->getRowIterator() as $row) {
 
                // It reads data after header. In the my excel sheet, 
                // header is in the first row. 
                if ($count > 1) { 
 
                    // Data of excel sheet
                    $data['name'] = $row[0];
                    $data['descrp'] = $row[1];
                    $data['category'] = $row[2];
                    $data['qty'] = $row[3];           
                    $data['density'] = $row[4];
                    $data['price'] = $row[5];
                    $data['date_added'] = $row[6];           
                    $data['molecularformula'] = $row[7];
                    $data['cas'] = $row[8];
                    $data['supplier'] = $row[9];           
                    $data['datereceived'] = $row[10];
                    $data['expierydate'] = $row[11];
                    $data['tarewight'] = $row[12];
                    $data['hazard'] = $row[13];
                    $data['location'] = $row[14];
                    $data['locationdetail'] = $row[15];
                    $data['chemicalstate'] = $row[16];
                    $data['owner'] = $row[17];
                    $data['safety'] = $row[18];
                     
                    
                    

                    //Here, You can insert data into database. 
                    print_r(data);
                     
                }
                $count++;
            }
        }
 
        // Close excel file
        $reader->close();
 
    } else {
 
        echo "Please Select Valid Excel File";
    }
 
} else {
 
    echo "Please Select Excel File";
     
}
?>
