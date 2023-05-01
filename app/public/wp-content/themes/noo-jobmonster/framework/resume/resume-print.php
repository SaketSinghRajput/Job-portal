<?php
function noo_create_print_resume(){
	ob_start();
    global $post;
    $post->ID = (!empty($_POST['resume'])) ? $_POST['resume'] : '';
    $upload_dir = wp_get_upload_dir();
    $upload_url = isset($upload_dir['baseurl']) ? $upload_dir['baseurl'] : '';
    wp_print_scripts('jquery');
    ?>
    <link rel="stylesheet" href="https://unpkg.com/@icon/dashicons/dashicons.css" type="text/css"  media="all" >
    <link rel="stylesheet" id="noo-indeed-css" href="<?php echo NOO_ASSETS_URI . '/css/noo.css' ?>" type="text/css" media="all">
    <link rel="stylesheet" id="noo-awesome-css"  href="<?php echo NOO_FRAMEWORK_URI . '/vendor/fontawesome/css/all.min.css' ?>" type="text/css"  media="all">
    <link rel="stylesheet" id="noo-boostrap-css" href="<?php echo NOO_FRAMEWORK_URI . '/vendor/bootstrap-multiselect/bootstrap-multiselect.css', null, null ?>" type="text/css" media="all">
    <link rel="stylesheet" id="noo-indeed-css" href="<?php echo NOO_FRAMEWORK_URI . '/vendor/genericons/genericons.css' ?>" type="text/css" media="all">
    <link rel="stylesheet" href="<?php echo $upload_url .'/noo_jobmonster/custom.css';?>" type="text/css" media="all" >
    <script src="<?php echo NOO_ASSETS_URI . '/vendor/rating/jquery.raty.js' ?>"></script>
    <script src="<?php echo NOO_ASSETS_URI . 'js/noo.js' ?>"></script>
    <link rel="stylesheet" id="noo-indeed-css" href="<?php echo  NOO_ASSETS_URI . '/vendor/rating/jquery.raty.css' ?>" type="text/css" media="all">
    <script>
        jQuery(window).load(function () {
            window.print();
        });
    </script>
    <style>
        .btn-print-resume{
            display: none!important;
        }
        @media print {
        	.resume-style-2 .resume-about{
        		display: none;
        	}
            .btn-print-resume{
                display: none!important;
            }
            .progress {
                position: relative;
            }           

            .progress:before {
                display: block;
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 0;
                border-bottom: 1em solid #eeeeee;
                border-radius: 10px;
            }

            .progress-bar {
                border-radius: 10px;
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 1;
                border-bottom: 1em solid #f5d006;;
            }

            .progress-bar-success {
                border-bottom-color: #67c600;
            }

            .progress-bar-info {
                border-bottom-color: #5bc0de;
            }

            .progress-bar-warning {
                border-bottom-color: #f0a839;
            }

            .progress-bar-danger {
                border-bottom-color: #ee2f31;
            }
        }
    </style>

    <?php
    
    do_action('noo_print_resume_layout_before');
    
    $layout = noo_get_option('noo_resumes_detail_layout', 'style-1');
    $layout = !empty($_POST['layout']) ? sanitize_text_field($_POST['layout']) : $layout;
   
    $layout = apply_filters('noo_print_resume_layout', $layout);
   
    if ('style-2' == $layout) {
    	noo_get_layout('candidate/resume_candidate_profile');
        noo_get_layout("resume/single/detail-style-2");
    } elseif ('style-3' == $layout) {
    	add_filter('jm_resume_show_candidate_contact', false);
        noo_get_layout("resume/single/detail-style-3");
    } elseif ('style-4' == $layout) {
        noo_get_layout("resume/single/detail-style-4");
    }else{
    	noo_get_layout("resume/single/detail");
    }
    
    do_action('noo_print_resume_layout_after');
    
    echo apply_filters('noo_print_resume_output', ob_get_clean());
    die;
}

add_action('wp_ajax_noo_create_print_resume', 'noo_create_print_resume');
add_action('wp_ajax_nopriv_noo_create_print_resume', 'noo_create_print_resume');
