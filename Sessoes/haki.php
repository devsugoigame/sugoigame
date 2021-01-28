<div class="panel-heading">
    Haki
</div>
<div class="panel-body">
    <?= ajuda("Haki", "<p>Haki é um modificador de combate que aumenta suas chances de se esquivar, bloquear ou acertar um 
golpe crítico.<br/> A cada nível evoluído, o personagem libera uma nova barra de treino para aumentar 1 ponto no Haki de sua
escolha.</p>
<p>Cada ponto em Mantra aumenta em 1% de chance do personagem se esquivar de um ataque. </p>
<p>Cada ponto em Armamento aumenta em 1% a chance do personagem bloquear um ataque e em 1% a chance do personagem 
acertar um ataque crítico.</p>") ?>

    <?php
    $full_haki = $connection->run("SELECT count(*) AS total FROM tb_personagens WHERE id = ? AND haki_lvl_ultima_era = ?",
        "ii", array($userDetails->tripulacao["id"], 25))->fetch_array()["total"];
    $recompensa = $connection->run("SELECT * FROM tb_recompensa_recebida_haki WHERE tripulacao_id = ?", "i", array($userDetails->tripulacao["id"]))->count();
    ?>
    <?php if ($full_haki && !$recompensa): ?>
        <h4>As recompensas pelo treino de Haki na era passada já estão disponíveis.</h4>
        <button class="link_send btn btn-success" href="link_Eventos\recompensa_haki.php">
            Receber as recompensas
        </button>
    <?php endif; ?>

    <?php $ontem = $connection->run('SELECT SUBDATE(CURRENT_DATE, INTERVAL 1 DAY) as ontem')->fetch_array()['ontem']; ?>
    <?php $hoje = $connection->run('SELECT CURRENT_DATE as hoje')->fetch_array()['hoje']; ?>

    <p>
        Toda vez que você treina com o mestre, você adquire pontos de Haki que podem ser aplicados á sua
        tripulação.<br/>
        Um novo treino é liberado a cada 6 horas a partir do início da Grande Era dos Piratas, e eles
        são acumulativos.
    </p>

    <?php
    $treinos_realizados = $connection->run("SELECT count(*) AS total FROM tb_haki_treino WHERE tripulacao_id = ?",
        "i", array($userDetails->tripulacao["id"]))->fetch_array()["total"];

    $time = time() - strtotime("2018-03-05 00:00:00");

    $treinos_total = ceil($time / (6 * 60 * 60));

    $treinos_possiveis = $treinos_total - $treinos_realizados;

    $treinos_limite = $treinos_total * 2 - $treinos_realizados;
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            Treinar Haki com o Mestre
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-9">
                    <?php if (!$treinos_limite): ?>
                        <p>Os treinos são liberados sempre as 6, 12, 18 e 24 horas.</p>
                    <?php else: ?>
                        <form class="ajax_form form-inline" action="Haki/atacar_mestre"
                              method="post"
                              data-question="Deseja enfrentar o mestre?">
                            <?php if ($treinos_limite == 1): ?>
                                <h4>Você já pode treinar com o mestre!</h4>
                            <input type="hidden" value="1" name="quant">
                            <?php else: ?>
                                <h4>
                                    Treinos acumulados: <?= $treinos_possiveis > 0 ? $treinos_possiveis : 0 ?>
                                </h4>
                                <h5>
                                    Você pode fazer <?= $treinos_limite - $treinos_possiveis ?> treinos extendidos
                                    pagando uma valor maior
                                </h5>

                                <div class="form-group">
                                    <label>Informe quantos treinos simultâneos deseja fazer (máximo 6):</label>
                                    <input id="select-quant-treino" class="form-control" name="quant" type="number"
                                           max="<?= min(6, $treinos_limite) ?>"
                                           min="1"
                                           value="1">
                                </div>

                                <script type="text/javascript">
                                    $(function () {
                                        $('#select-quant-treino').on('change', function () {
                                            var quant = parseInt($(this).val(), 10);
                                            var precoUnitario = quant > <?= $treinos_possiveis ?> ? 2000000 : 1000000;
                                            $('#berries-treinar-mestre').html(mascaraBerries(quant * precoUnitario + (quant - 1) * precoUnitario));
                                        });
                                    });
                                </script>
                            <?php endif; ?>
                            <div>
                                <p>Custo:</p>
                                <div>
                                    <img src="Imagens/Icones/Berries.png"/>
                                    <span id="berries-treinar-mestre">2.000.000</span>
                                </div>
                            </div>
                            <button class="btn btn-success">
                                Treinar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <img src="Imagens/Batalha/npc3.png" width="100%">
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <h3>
                Haki disponível:
                <?= mascara_numeros_grandes($userDetails->tripulacao["haki_xp"]) ?>
            </h3>
        </div>
    </div>

    <div>
        <?php render_personagens_pills() ?>
    </div>

    <div class="tab-content">
        <?php foreach ($userDetails->personagens as $index => $pers): ?>
            <?php render_personagem_panel_top($pers, $index) ?>

            <div class="panel panel-default">
                <div class="panel-body">
                    <p>
                        Toda vez que um tripulante adquire pontos suficientes para completar sua barra de Haki ele
                        evolui um nível de Haki e recebe um ponto para distribuir entre Mantra ou Armamento.
                    </p>
                    <h4>Nível de Haki <?= $pers["haki_lvl"] ?>/<?= HAKI_LVL_MAX ?></h4>

                    <div class="progress">
                        <div class="progress-bar" style="width: <?= $pers["haki_xp"] / $pers["haki_xp_max"] * 100 ?>%">
                            Pontos:<?= $pers["haki_xp"] . "/" . $pers["haki_xp_max"] ?>
                        </div>
                    </div>
                    <?php if ($userDetails->tripulacao["haki_xp"] && $pers["haki_lvl"] < HAKI_LVL_MAX): ?>
                        <form class="ajax_form form-inline" action="Haki/haki_treinar"
                              data-question="Deseja aplicar estes pontos de Haki para esse tripulante?"
                              method="post">
                            <input type="hidden" name="pers" value="<?= $pers["cod"] ?>">
                            <div class="form-group">
                                <label>Aplicar Haki:</label>
                                <input class="form-control" name="quant" type="number"
                                       max="<?= min($pers["haki_xp_max"] - $pers["haki_xp"], $userDetails->tripulacao["haki_xp"]) ?>"
                                       min="1"
                                       value="<?= min($pers["haki_xp_max"] - $pers["haki_xp"], $userDetails->tripulacao["haki_xp"]) ?>">
                            </div>
                            <button class="btn btn-success">
                                Aplicar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Pontos a distribuir: <?= $pers["haki_pts"] ?>
                </div>
                <div class="panel-body">
                    <?php if ($pers["haki_pts"] > 0) : ?>
                        <div class="text-right">
                            <button href="link_Haki/haki_distribuir.php?pers=<?= $pers["cod"]; ?>&tipo=1"
                                    class="link_send btn btn-info">
                                <i class="fa fa-plus"></i> Mantra (1 ponto)
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php render_personagem_mantra_bar($pers); ?>

                    <?php if ($pers["haki_pts"] > 0) : ?>
                        <div class="text-right">
                            <button href="link_Haki/haki_distribuir.php?pers=<?= $pers["cod"]; ?>&tipo=2"
                                    class="link_send btn btn-info">
                                <i class="fa fa-plus"></i> Armamento (1 ponto)
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php render_personagem_armamento_bar($pers); ?>

                    <?php if ($pers["cod"] == $userDetails->capitao["cod"]) : ?>
                        <?php if ($pers["haki_pts"] > 0) : ?>
                            <div class="text-right">
                                <button href="link_Haki/haki_distribuir.php?pers=<?= $pers["cod"]; ?>&tipo=3"
                                        class="link_send btn btn-info">
                                    <i class="fa fa-plus"></i> Haoshoku (1 ponto)
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php render_personagem_hdr_bar($pers); ?>
                        <?php if (isset($COD_HAOSHOKU_LVL[$pers["haki_hdr"] + 1])): ?>
                            <p>
                                O seu capitão pode optar por evoluir um tipo especial de Haki: o Haki do Rei.<br/>
                                Esse Haki concede uma habilidade que fica mais forte a cada ponto aplicado.
                            </p>
                            <?php $hdr = $connection->run("SELECT * FROM tb_skil_atk WHERE cod_skil = ?",
                                "i", array($COD_HAOSHOKU_LVL[$pers["haki_hdr"] + 1])); ?>
                            <div class="text-left">
                                <h4>No próximo nível, o Haoshoku será uma habilidade com os seguintes efeitos:</h4>
                                <?php render_skill_efeitos($hdr->fetch_array()); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <p>
                        <button href="Vip/reset_haki.php?cod=<?= $pers["cod"]; ?>&tipo=gold"
                            <?= $userDetails->conta["gold"] >= PRECO_GOLD_RESET_HAKI ? "" : "disabled" ?>
                                data-question="Resetar pontos de haki desse personagem?"
                                class="bt_reset_haki link_confirm btn btn-info">
                            <?= PRECO_GOLD_RESET_HAKI ?> <img src="Imagens/Icones/Gold.png" height="15px"/> Resetar
                            Pontos
                        </button>
                        <button href="Vip/reset_haki.php?cod=<?= $pers["cod"]; ?>&tipo=dobrao"
                            <?= $userDetails->conta["dobroes"] >= PRECO_DOBRAO_RESET_HAKI ? "" : "disabled" ?>
                                data-question="Resetar pontos de haki desse personagem?"
                                class="bt_reset_haki link_confirm btn btn-info">
                            <?= PRECO_DOBRAO_RESET_HAKI ?> <img src="Imagens/Icones/Dobrao.png" height="15px"/> Resetar
                            Pontos
                        </button>
                    </p>
                </div>
            </div>
            <?php render_personagem_panel_bottom() ?>
        <?php endforeach; ?>
    </div>
</div>