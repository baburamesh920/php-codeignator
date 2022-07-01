<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Images Management
            <small>Add / Edit Image</small>
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Enter Card Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="addUser" action="<?php echo base_url() ?>addNewImage" method="post"
                          role="form" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="role">Card Name</label>
                                <input class="form-control required" id="image_name" name="imageName" type="text"/>
                            </div>

                            <div class="form-group">
                                <label for="role">Category</label>
                                <select class="form-control required" id="categoryId" name="categoryId">
                                    <option value=" ">Select Parent Category</option>
                                    <?php
                                    if (!empty($categories)) {
                                        foreach ($categories as $rl) {

                                            ?>
                                            <option value="<?php echo $rl->categoryId ?>" <?php if ($rl->categoryId == set_value('role')) {
                                                echo "selected=selected";
                                            } ?>><?php echo $rl->categoryName ?></option>

                                            <?php
                                            if (!empty($rl->sub)) {
                                                foreach ($rl->sub as $rlsub) { ?>
                                                    <option value="<?php echo $rlsub->categoryId ?>">
                                                        -<?php echo $rlsub->categoryName ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="role">Card Link</label>
                                <input class="form-control required" id="image_name" name="imageLink" type="text"/>
                            </div>

                            <div class="form-group">
                                <label for="role">Card Tags</label>
                                <input class="form-control required" id="image_name" name="imageTags" type="text"/>
                            </div>

                            <div>
                                <input type='file' id="image" name="image[]" multiple/>
                                <div class="gallery"></div>
                            </div>


                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Submit"/>
                            <input type="reset" class="btn btn-default" value="Reset"/>
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
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if ($success) {
                    ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php } ?>

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
    // function readURL(input) {
    //     if (input.files && input.files[0]) {
    //         var reader = new FileReader();
    //
    //         reader.onload = function (e) {
    //             $('#imagepreview').attr('src', e.target.result);
    //         }
    //
    //         reader.readAsDataURL(input.files[0]);
    //     }
    // }
    //
    // $("#image").change(function () {
    //     readURL(this);
    // });

    $(function () {
        // Multiple images preview in browser
        var imagesPreview = function (input, placeToInsertImagePreview) {

            if (input.files) {
                var filesAmount = input.files.length;

                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();

                    reader.onload = function (event) {
                        $($.parseHTML('<img>')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    }

                    reader.readAsDataURL(input.files[i]);
                }
            }

        };

        $('#image').on('change', function () {
            imagesPreview(this, 'div.gallery');
        });
    });
</script>