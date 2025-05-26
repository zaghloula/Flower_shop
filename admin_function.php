<?php
function check_login($conn)
{
   if(isset( $_SESSION['admin_id']))
   {
       $id=$_SESSION['admin_id'];
       $query="select * from admin where admin_id ='$id' limit 1";

       $result=mysqli_query($conn, $query);

       if($result && mysqli_num_rows($result) >0)
       {
           $admin_data=mysqli_fetch_assoc($result);
           return $admin_data;
       }
   }

   //redirect to login

   header("Location:edit_flower.php");
   die;

}
?>
