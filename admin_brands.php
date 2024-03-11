<?php
    include 'connection.php';
    session_start();
    $admin_id = $_SESSION['admin_name'];

    if(!isset($admin_id)){
        header('location:login.php');
    }

    if(isset($_POST['logout'])){
        session_destroy();
        header('location:login.php');
    }

    //adding product to database
    if (isset($_POST['add_brands'])) {
        $brand_name = mysqli_real_escape_string($conn, $_POST['name']);
        $brand_image = $_FILES['image']['name'];
        $brand_image_tmp_name = $_FILES['image']['tmp_name'];
        $brand_image_folder = 'image/' . $brand_image;
    
        // Kiểm tra xem tên thương hiệu đã tồn tại hay chưa
        $select_brand_name = mysqli_query($conn, "SELECT name FROM brands WHERE name = '$brand_name'") or die('query failed');
        if (mysqli_num_rows($select_brand_name) > 0) {
            $message[] = 'Tên thương hiệu đã tồn tại';
        } else {
            // Thêm thương hiệu mới vào cơ sở dữ liệu
            $insert_brand = mysqli_query($conn, "INSERT INTO brands(name, image) VALUES('$brand_name', '$brand_image')") or die('query failed');
    
            // Nếu thêm thương hiệu thành công, thì tải lên hình ảnh thương hiệu
            if ($insert_brand) {
                if (move_uploaded_file($brand_image_tmp_name, $brand_image_folder)) {
                    $message[] = 'Thương hiệu đã được thêm thành công';
                } else {
                    $message[] = 'Không thể tải lên hình ảnh thương hiệu';
                }
            } else {
                $message[] = 'Không thể thêm thương hiệu: ' . mysqli_error($conn);
            }
        }
    }

    //delete product from database
// // Kiểm tra xem có tồn tại biến GET 'delete' hay không
// if (isset($_GET['delete'])) {
//     // Lấy ID thương hiệu cần xóa
//     $delete_id = $_GET['delete'];

//     // Truy vấn lấy ảnh thương hiệu cần xóa
    // $select_delete_image = mysqli_query($conn, "SELECT image FROM `brands` WHERE id='$delete_id'") or die('query failed');
    // $fetch_delete_image = mysqli_fetch_assoc($select_delete_image);

//     // Xoá ảnh thương hiệu khỏi thư mục
//     // unlink('image/' . $fetch_delete_image['image']);

//     // Xoá thương hiệu khỏi bảng brands
//     mysqli_query($conn, "DELETE FROM `brands` WHERE id = '$delete_id'") or die('query faild');

//     // Xoá sản phẩm liên quan đến thương hiệu khỏi bảng products
//     mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query faild');

//     // Xoá sản phẩm liên quan đến thương hiệu khỏi bảng cart
//     mysqli_query($conn, "DELETE FROM `cart` WHERE pid = '$delete_id'") or die('query faild');

//     // Xoá sản phẩm liên quan đến thương hiệu khỏi bảng wishlist
//     mysqli_query($conn, "DELETE FROM `wishlist` WHERE pid = '$delete_id'") or die('query failed');

//     // Chuyển hướng đến trang admin_brands.php
//     header('location:admin_brands.php');
// }


if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $select_delete_image = mysqli_query($conn, "SELECT image FROM `brands` WHERE id='$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($select_delete_image);
    mysqli_query($conn, "DELETE FROM `brands` WHERE id = '$delete_id'") or die('query faild');
    // unlink('image/'.$fetch_delete_image['image']);
    // mysqli_query($conn,"DELETE FROM `products` WHERE id = '$delete_id'") or die('query faild');
    // mysqli_query($conn,"DELETE FROM `cart` WHERE pid = '$delete_id'") or die('query faild');
    // mysqli_query($conn,"DELETE FROM `wishlist` WHERE pid = '$delete_id'") or die('query failed');

    header('location:admin_brands.php');
}

    //update product
    if(isset($_POST['updte_product'])){
        $update_id = $_POST['update_id'];
        $update_name = $_POST['update_name'];
        $update_price = $_POST['update_price'];
        $update_detail = $_POST['update_detail'];
        $update_image = $_FILES['update_image']['name'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'image/'.$update_image;

        $update_query = mysqli_query($conn,"UPDATE `products` SET id ='$update_id', name = '$update_name', price ='$update_price', product_detail ='$update_detail', image ='$update_image' WHERE id ='$update_id'") or die('query failed');
        if($update_query){
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            header('location:admin_product.php');
        }
        
        
    }
    if (isset($_POST['cancel-form'])) {
        header('location:admin_product.php');
        exit();
    }

    



?>
<style type="text/css">
    <?php
        include 'style.css';
    ?>
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="style.css?v=1.1 <?php echo time();?>">
    <title>Admin Product</title>
</head>
<body>
    <?php include 'admin_header.php';?>
    <?php
        if(isset($message)){
            foreach($message as $message){
                echo '
                    <div class="message">
                        <span>'.$message.'</span>
                        <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
                    </div>
                ';
            }
        }
    ?>

    <div class="line2"></div>
    <section class="add-products form-container">
        <form method="POST" action="" enctype="multipart/form-data">

            <div class="input-field">
                <label>Tên thương hiệu<span>*</span><br></label>
                <input type="text" name="name" required>
            </div>
            <div class="input-field">
                <label>Hình ảnh thương hiệu<span>*</span></label>
                <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" required>
            </div>
            <input type="submit" name="add_brands" value="Thêm thương hiệu" class="btn">
         </form>
    </section>
    <div class="line3"></div>
    <div class="line4"></div>
    <section class="show-products">
        <div class="box-container">
            <?php
                $select_brands = mysqli_query($conn, "SELECT * FROM `brands`") or die('query failed');
                if (mysqli_num_rows($select_brands) > 0) {
                    while ($fetch_brands = mysqli_fetch_assoc($select_brands)) {
            ?>
            <div class="box">
                <img src="image/<?php echo $fetch_brands['image'];?>">
                <h4><?php echo $fetch_brands['name'];?></h4>
                <a href="admin_brands.php?edit=<?php echo $fetch_brands['id'];?>" class="edit">Sửa</a>
                <a href="admin_brands.php?delete=<?php echo $fetch_brands['id'];?>" class="delete" onclick="
                    return confirm('Bạn có chắc muốn xóa sản phẩm này');">Xóa</a>
            </div>
            <?php
                    } 
                }else{
                        echo'
                            <div class="empty">
                                <p>Chưa có thương hiệu được thêm!</p>
                            </div>
                        ';            
                    } 
            ?>

        </div>

    </section>
    


    <div class="line"></div>

    <script type="text/javascript">
        document.getElementById('cancel-form').addEventListener('click', function() {
            window.location.href = 'admin_brands.php';
        });
    </script>
    <script type="text/javascript" src="script.js"></script>
</body>
</html>