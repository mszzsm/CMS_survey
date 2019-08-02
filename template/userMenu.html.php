    <header>
        <nav class="navbar navbar-light navbar-expand-md bg-light border-bottom ">
            <a class="navbar-brand" href="<?php echo $mainDir; ?>"><img src="<?php echo $mainDir; ?>img/sanden.png" width="250" class="d-inline-block" alt="SMP"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
                    
            <div class="collapse navbar-collapse" id="mainmenu">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $mainDir; ?>survey/main/">Lista ankiet</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"  href="<?php echo $mainDir; ?>survey/create/">Nowa ankieta</a>
                    </li>
                </ul>
            </div>
            <?php $user = $this->reg->getSession('userName'); ?>
            <a class="navbar-text d-none d-md-inline-block">Witaj, <strong><?php echo $user; ?></strong><a href="<?php echo $mainDir; ?>user/signOff/"><i class="fa fa-sign-out-alt d-none d-md-inline-block" style="margin-left:10px; color:#666"></i></a></a>
        </nav>
    </header>