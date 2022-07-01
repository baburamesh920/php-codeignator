<?php
$adsImage = $adsInfo->adsImage;
$adsId = $adsInfo->adsId;
$adsLink = $adsInfo->adsLink;
$adsTitle = $adsInfo->adsTitle;
?>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Ads Management
            <small>Add / Edit Ads</small>
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Edit Ads Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper('form'); ?>
                    <form role="form" id="addUser" action="<?php echo base_url(); ?>editAdsPost" method="post" role="form" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="hidden" value="<?php echo $adsId; ?>" name="adsId" id="adsId" />
                            <div class="form-group">
                                <label for="fname">Ad Title</label>
                                <input type="text" class="form-control required" value="<?php echo $adsTitle; ?>" id="adsTitle" name="adsTitle" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="fname">Ad Link</label>
                                <input type="text" class="form-control required" value="<?php echo $adsLink; ?>" id="adsLink" name="adsLink" maxlength="255">
                            </div>
                            <!--<img src="<?php echo base_url($imagePath); ?>"  height=100px width=100px >-->

                            <div>
                                <input type='file' id="image" name="image" />
                                <img id="imagepreview" src="<?php echo base_url($adsImage); ?>" alt="your image" style="max-height:150px; max-width:300px;" />
                            </div>


                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <input type="reset" class="btn btn-default" value="Reset" />
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if ($error) {
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php
                } ?>
                <?php
                $success = $this->session->flashdata('success');
                if ($success) {
                    ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php
                } ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
<!-- <script src="<?php echo base_url(); ?>assets/js/addUser.js" type="text/javascript"></script> -->
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#imagepreview').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#image").change(function() {
        readURL(this);
    });
</script>