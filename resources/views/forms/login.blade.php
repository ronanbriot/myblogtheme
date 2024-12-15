<div class="pt-8 sm:pt-20">
    <form name="loginform" id="loginform" action="https://my-blog.kakii.fr/wp/wp-login.php" method="post">
        <div class="login-username w-96 mb-5">
            <label class="label label-text text-base-content" for="user_login">Identifiant ou adresse e-mail</label>
            <input type="text" name="log" id="user_login" autocomplete="username" class="input" value="" size="20" />
        </div>
        <div class="login-password w-96 mb-5">
            <label class="label label-text text-base-content" for="user_pass">Mot de passe</label>
            <input type="password" name="pwd" id="user_pass" autocomplete="current-password" spellcheck="false" class="input" value="" size="20" />
        </div>
        <div class="login-remember flex items-center gap-1 mb-5">
            <input name="rememberme" type="checkbox" id="rememberme" class="checkbox" value="forever" />
            <label class="label label-text text-base-content" for="rememberme">Se souvenir de moi</label>
        </div>
        <p class="login-submit">
            <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary" value="Se connecter" /> <input type="hidden" name="redirect_to" value="https://my-blog.kakii.fr/anniversaire-eliane/" />
        </p>
    </form>
</div>