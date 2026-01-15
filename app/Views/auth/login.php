<div class="row justify-content-center">
    <div class="col-12 col-sm-8 col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 mb-3 text-center">Login</h1>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Sign in</button>
                </form>
            </div>
        </div>
    </div>
</div>