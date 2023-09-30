<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <div class="row">
        <?php if(isset($model["error"])) : ?>
        <div class="alert alert-danger" role="alert"><?=$model["error"]?></div>
        <?php endif; ?>
    </div>
    <div class="row align-items-center g-lg-5 py-5">
        <div class="col-lg-7 text-center text-lg-start">
            <h1 class="display-4 fw-bold lh-1 mb-3">Profile</h1>
            <p class="col-lg-10 fs-4">by <a target="_blank" href="https://www.programmerzamannow.com/">Hendy Nur
                    Sholeh</a></p>
        </div>
        <div class="col-md-10 mx-auto col-lg-5">
            <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/users/profile">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="id" placeholder="id" disabled
                        value="<?= $model['form']['id']; ?>" name="id" />
                    <label for="id">Id</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="username" type="text" class="form-control" id="name"
                        value="<?=$model['form']['username']?>" placeholder="name" />
                    <label for="name">Name</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</div>