<?php

require_once "Includes/conectdb.php";
include_once "Includes/verifica_login.php";
include_once "Includes/verifica_combate.php";
include_once "Includes/verifica_missao.php";

if (!isset($_GET["sessao"]) || !validate_alphanumeric($_GET["sessao"])) {
    echo "#Sessão inválida";
    exit();
}

$sessao = $_GET["sessao"];

if (!file_exists("Sessoes/" . $sessao . ".php")) {
    $sessao = "home";
}

$protector->protect($sessao);

$facebook_url = "https://www.facebook.com/dialog/oauth?client_id=296635354108298&redirect_uri=http://localhost/Scripts/Geral/login_facebook.php&scope=email";
?>
    <div id="header">
        <div id="header_barra">
            <?php if (!$userDetails->conta): ?>
                <nav class="header-navbar navbar navbar-default">
                    <div class="container-fluid">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="login.php">Início</a>
                            </li>
                            <li>
                                <a href="?ses=cadastro" class="link_content">Cadastrar</a>
                            </li>
                            <li>
                                <a class="bt-facebook" href="<?= $facebook_url ?>">
                                    <i class="fa fa-facebook-square"></i> Entrar com Facebook
                                </a>
                                <script type="text/javascript">

                                    $('.bt-facebook').on('click', function () {
                                        fbq('track', 'FacebookLogin');
                                    });
                                </script>
                            </li>
                        </ul>
                        <form class="navbar-form navbar-right" name="login" action="Scripts/Geral/logar.php"
                              method="POST">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                    <input type="email" name="login" class="form-control" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-key"></i></div>
                                    <input type="password" name="senha" class="form-control" placeholder="Senha"
                                           required>
                                </div>
                            </div>
                            <button class="btn btn-info" type="submit">Jogar</button>
                        </form>
                    </div>
                </nav>
            <?php elseif ($userDetails->tripulacao) :
                include "header.php";
            else: ?>
                <nav class="header-navbar navbar navbar-default">
                    <div class="container-fluid">
                        <div>
                            <ul class="nav navbar-nav">
                            </ul>
                        </div>
                    </div>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    <div id="fundo">
        <?php if ($userDetails->conta["ativacao"]): ?>
            <div class="alert alert-danger" role="alert">
                Sua conta ainda não foi ativada. Quando ativar sua conta você receberá
                600 Dobrões de Ouro.
                <a href="?ses=cadastrosucess" class="link_content">Clique aqui</a>
                para inserir o código de ativação que foi enviado para o seu E-mail.
            </div>
        <?php endif; ?>
        <div class="container-fluid">
            <?php if ($userDetails->tripulacao) : ?>
                <div class="row">
                    <div id="tripulantes-bar" class="col-xs-12">
                        <?php include "tripulantes.php"; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="container-fluid fundo-container">
            <div class="row main-container-row">
                <?php if (!$protector->is_ful_wide_session($sessao)): ?>
                    <div class="col-sm-3 menu-col">
                        <?php if ($sessao == "oceano"): ?>
                            <div class="text-left" style="margin-left: 5px">
                                <button class="btn btn-default" data-toggle="collapse" href="#menu-cover">
                                    <i class="fa fa-bars"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                        <div id="menu-cover" class="collapse in">
                            <div class="menu-content">
                                <?php include "menu.php"; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col-sm-<?= $protector->is_ful_wide_session($sessao) ? "12" : "9" ?> main-col">
                    <div class="panel panel-default main-panel">
                        <?php include "Sessoes/$sessao.php"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="footer">
        <div id="footer_cima">
            <div id="footer_cr">
                Sugoi Game 2012 - <a href="?ses=politica" class="link_content">Política de Privacidade</a> -
                <a href="?ses=regras" class="link_content">Regras e Punições</a><br/>
                Personagens e desenhos © CopyRight 1997 by Eiichiro Oda. Todos os direitos reservados
            </div>
        </div>
    </div>

<?php if ($userDetails->tripulacao): ?>
    <button id="chat-button" class="btn btn-default">
        <i class="fa fa-comments"></i> CHAT
    </button>
    <input type="hidden" id="ilha_atual" value="<?= $userDetails->ilha["ilha"]; ?>">
    <input type="hidden" id="coord_x_navio" value="<?= $userDetails->tripulacao["x"]; ?>">
    <input type="hidden" id="coord_y_navio" value="<?= $userDetails->tripulacao["y"]; ?>">
    <input type="hidden" id="sg_c" value="<?= $_SESSION["sg_c"]; ?>">
    <input type="hidden" id="sg_k" value="<?= $_SESSION["sg_k"]; ?>">
<?php endif; ?>