 <div style="margin-top:0px;" class="row no-margin">

        <iframe  width="100%" height="250"  style="border:0;" allowfullscreen="" loading="lazy" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1932.3821714331211!2d120.97689667264869!3d14.383042080242037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d3b2987a57cb%3A0x3f09c4d999e3467a!2sComia%20Dental%20Clinic%20(Molino%20-%20Bacoor%20Branch)!5e0!3m2!1sen!2sph!4v1654064264022!5m2!1sen!2sph" height="450" frameborder="0" style="border:0" allowfullscreen"></iframe>

    </div> 

<div class="col-12">
    <div class="row my-5 ">
        <div class="col-md-5">

            <div class="card card-outline card-navy rounded-0 shadow">
                <div class="card-header">
                    <h4 class="card-title">Contact Information</h4>
                </div>
                <div class="card-body rounded-0">
                    <dl>
                        <dt class="text-muted"><i class="fa fa-envelope"></i> Email</dt>
                        <dd class="pl-4"><?= $_settings->info('email') ?></dd>
                        <dt class="text-muted"><i class="fa fa-phone"></i> Contact #</dt>
                        <dd class="pl-4"><?= $_settings->info('contact') ?></dd>
                        <dt class="text-muted"><i class="fa fa-map-marked-alt"></i> Location</dt>
                        <dd class="pl-4"><?= $_settings->info('address') ?></dd>
                        <dt class="text-muted"><i class="fa fa-clock"></i> Daily Schedule</dt>
                        <dd class="pl-4"><?= $_settings->info('clinic_schedule') ?></dd>
                        <dt class="text-muted"><i class="fa fa-paw"></i> Maximum Daily Appointments</dt>
                        <dd class="pl-4"><?= $_settings->info('max_appointment') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card rounded-0 card-outline card-navy shadow" >
                <div class="card-body rounded-0">
                    <h2 class="text-center">Message Us</h2>
                    <center><hr class="bg-navy border-navy w-25 border-2"></center>
                    <?php if($_settings->chk_flashdata('pop_msg')): ?>
                        <div class="alert alert-success">
                            <i class="fa fa-check mr-2"></i> <?= $_settings->flashdata('pop_msg') ?>
                        </div>
                        <script>
                            $(function(){
                                $('html, body').animate({scrollTop:0})
                            })
                        </script>
                    <?php endif; ?>
                    <form action="" id="message-form">
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm form-control-border" id="fullname" name="fullname" required placeholder="John Smith">
                                <small class="px-3 text-muted">Full Name</small>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm form-control-border" id="contact" name="contact" required placeholder="xxxxxxxxxxxxx">
                                <small class="px-3 text-muted">Contact #</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="email" class="form-control form-control-sm form-control-border" id="email" name="email" required placeholder="xxxxxx@xxxxxx.xxx">
                                <small class="px-3 text-muted">Email</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <small class="text-muted">Message</small>
                                <textarea name="message" id="message" rows="4" class="form-control form-control-sm rounded-0" required placeholder="Write your message here"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12 text-center">
                                <button class="btn btn-primary rounded-pill col-5">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#message-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_message",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload();
                    }else if(!!resp.msg){
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                    }else{
                        el.addClass("alert-danger")
                        el.text("An error occurred due to unknown reason.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    $('html, body').animate({scrollTop:0},'fast')
                    end_loader();
                }
            })
        })
    })
</script>