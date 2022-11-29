<?php
function ms_get_attachment_url(){
    
    if( !empty($_POST) ){
        
        $type = get_post_mime_type( $_POST['attachment_id'] );    

        $file_path = get_attached_file( $_POST['attachment_id'] );

        $ms_genrated_image = get_post_meta($_POST['attachment_id'],'ms_genrated_image',true);

        if( empty($ms_genrated_image) ){

            require_once __DIR__ . '/liberoffice/autoload.php';

            $converter = new OfficeConverter($file_path);

            $rand_name = "ms_".rand().'.pdf';

            $converter->convertTo($rand_name);

            $pdf_file = WP_CONTENT_DIR."/ms_pdf_files/".$rand_name;

            $im = new imagick($pdf_file);
            $noOfPagesInPDF = $im->getNumberImages();
            if( !empty($noOfPagesInPDF) ){
                $noOfPagesInPDF = 1;
                for ($i = 0; $i < $noOfPagesInPDF; $i++) {
                    $url = $pdf_file.'['.$i.']'; 
                    $image = new Imagick($url);
                    $image->setImageFormat("jpg"); 
                    $rand_image_name = ($i+1).'-'.rand().'.jpg';
                    $image->writeImage(WP_CONTENT_DIR.'/ms_pdf_files/'.$rand_image_name);
                    update_post_meta($_POST['attachment_id'],'ms_genrated_image',$rand_image_name);
                    echo home_url().'/wp-content/ms_pdf_files/'.$rand_image_name;
                }
            }
        }else{
            $ms_genrated_image = get_post_meta($_POST['attachment_id'],'ms_genrated_image',true);
            echo home_url().'/wp-content/ms_pdf_files/'.$ms_genrated_image;
        }
    }
    die;
}
?>
