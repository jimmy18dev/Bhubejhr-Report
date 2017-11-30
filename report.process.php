<?php
require_once'autoload.php';
header("Content-type: text/json");

$image      = new Image();
$report     = new Report();

$report_id  = $_POST['report_id'];
$name       = $_POST['name'];
$desc       = $_POST['desc'];
$url        = $_POST['url'];

if(!empty($report_id) && isset($report_id)){
    $report->edit($report_id,$name,$desc,$url);
}else{
    $report_id  = $report->create($name,$desc,$url);
}

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    if(isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])){
        $image_name       = $_FILES['image']['name'];
        $image_size       = $_FILES['image']['size'];
        $image_temp       = $_FILES['image']['tmp_name'];
        $image_size_info  = getimagesize($image_temp);

        if($image_size_info){
            $im_width       = $image_size_info[0];
            $im_height      = $image_size_info[1];
            $im_type        = $image_size_info['mime'];
            $im_format      = $image->PhotoFormat($im_width,$im_height);

            switch($im_type){
                case 'image/png':
                    $image_res = imagecreatefrompng($image_temp); break;
                case 'image/gif':
                    $image_res = imagecreatefromgif($image_temp); break;
                case 'image/jpeg': case 'image/pjpeg':
                    $image_res = imagecreatefromjpeg($image_temp); break;
                default:
                    $image_res = false;
            }

            if($image_res){
                $image_info             = pathinfo($image_name);
                $image_extension        = strtolower($image_info["extension"]);
                $new_file_name          = md5(time().rand(0,9999999999)).'.'.$image_extension;

                $save_as['thumbnail']   = $destination_folder['thumbnail'].$new_file_name;
                $save_as['square']      = $destination_folder['square'].$new_file_name;
                $save_as['normal']      = $destination_folder['normal'].$new_file_name;

                $image->resize($image_res,$save_as['normal'],$im_type,$size['normal'],$im_width,$im_height,$quality['normal']);
                $image->square($image_res,$save_as['square'],$im_type,$size['square'],$im_width,$im_height,$quality['square']);
                $image->square($image_res,$save_as['thumbnail'],$im_type,$size['thumbnail'],$im_width,$im_height,$quality['thumbnail']);

                // DELETE OLD IMAGE
                // $content->get($content_id);
                // if(!empty($content->image_file)){
                //     unlink($destination_folder['thumbnail'].$content->image_file);
                //     unlink($destination_folder['square'].$content->image_file);
                //     unlink($destination_folder['normal'].$content->image_file);
                //     unlink($destination_folder['large'].$content->image_file);
                // }

                $image->save($user->id,$report_id,$new_file_name,$image_alt,$im_format,'cover');
                imagedestroy($image_res);
            }
        }
    }
}

$data = array(
    "apiVersion"    => 1.0,
    "message"       => 'Image Upload!',
    "content_id"    => $content_id,
    "image_file"    => $new_file_name,
    "alt"           => $alt,
    "execute"       => floatval(round(microtime(true)-StTime,4)),
);

echo json_encode($data);
?>