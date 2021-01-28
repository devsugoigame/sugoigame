<?php
function filter_personagem_derrotado($pers) {
    return $pers["respawn_tipo"] == RECUPERACAO_TIPO_POUSADA || ($pers["hp"] > 0 && $pers["hp"] < $pers["hp_max"] && $pers["respawn_tipo"] == 0);
}

function filter_personagem_pode_iniciar($pers) {
    return ($pers["hp"] > 0 && $pers["hp"] < $pers["hp_max"] && $pers["respawn_tipo"] == 0);
}

function filter_personagem_pode_finalizar($pers) {
    return $pers["respawn_tipo"] == RECUPERACAO_TIPO_POUSADA && $pers["respawn"] < atual_segundo();
}

?>
<div class="panel-heading">
    Pousada
</div>
<script type="text/javascript">
    $(function () {
        timeOuts["atualiza_tempo_hospital"] = setTimeout("atualiza_tempo_hospital()", 1000);
    });
    var conttmp = 0;
    function atualiza_tempo_hospital() {
        timeOuts["atualiza_tempo_hospital"] = setTimeout("atualiza_tempo_hospital()", 1000);
        for (x = 0; x < 20; x++) {
            var sec_rest = "tempo_sec_rest_" + x;
            var min_rest = "tempo_min_rest_" + x;
            if (document.getElementById(sec_rest) != null) {
                var tmp = document.getElementById(sec_rest).value - conttmp;
                document.getElementById(min_rest).innerHTML = transforma_tempo(tmp);
                if (tmp < 0) {
                    var nome = $('#nome-' + x).val();
                    var img = $('#img-' + x).val();
                    enviarNotificacao('Descanso concluído!', {
                        body: 'O descanso de ' + nome + ' na pousada já foi concluído.',
                        icon: img
                    });
                    reloadPagina();
                }
            }
        }
        conttmp += 1;
    }
</script>
<div class="panel-body">
    <?= ajuda("Pousada", "Aqui sua tripulação pode restaurar as energias enquanto descansa por alguns momentos.") ?>

    <?php $personagens = array_filter($userDetails->personagens, "filter_personagem_derrotado"); ?>
    <div>
        <?php if (count($personagens)) : ?>
            <p>
                <?php $personagens_iniciar = array_filter($personagens, "filter_personagem_pode_iniciar"); ?>
                <?php if (count($personagens_iniciar)): ?>
                    <button class="btn btn-info link_send" href='link_Pousada/pousada_iniciar_recuperacao.php'>
                        Iniciar o tratamento de toda a tripulação
                    </button>
                <?php endif; ?>
                <?php $personagens_finalizar = array_filter($personagens, "filter_personagem_pode_finalizar"); ?>
                <?php if (count($personagens_finalizar)): ?>
                    <button class="btn btn-success link_send" href='link_Pousada/pousada_finalizar_recuperacao.php'>
                        Finalizar o tratamento de toda a tripulação
                    </button>
                <?php endif; ?>
            </p>
            <div>
                <?php render_personagens_pills($personagens); ?>
            </div>
        <?php else: ?>
            <p>Todos os seus personagens estão bens por enquanto.</p>
        <?php endif; ?>
    </div>
    <div class="tab-content">
        <?php foreach ($personagens as $index => $pers): ?>
            <?php render_personagem_panel_top($pers, $index) ?>
            <?php render_personagem_sub_panel_with_img_top($pers); ?>
            <div class="panel-body">
                <?php if (!$pers["respawn"]) : ?>
                    <?php $tempo = max(0, 10 * ($pers["lvl"] - 20)); ?>
                    <p>
                        <strong>Tempo de espera:</strong>
                        <?= transforma_tempo_min($tempo) ?>
                    </p>

                    <button href='link_Pousada/pousada_iniciar_recuperacao.php?cod=<?= $pers["cod"]; ?>'
                            class="link_send btn btn-primary">
                        Iniciar descanso
                    </button>
                <?php elseif ($pers["respawn_tipo"] == RECUPERACAO_TIPO_POUSADA) : ?>
                    <?php $tempo = $pers["respawn"] - atual_segundo(); ?>

                    <?php if ($tempo > 0) : ?>
                        <input type="hidden" id="nome-<?= $index ?>" value="<?= $pers["nome"] ?>">
                        <input type="hidden" id="img-<?= $index ?>"
                               value="http://localhost/Imagens/Personagens/Icons/<?= get_img($pers, "r") ?>.jpg">
                        <p>
                            <strong>Tempo Restante: </strong>
                            <span id="tempo_min_rest_<?= $index ?>"><?= transforma_tempo_min($tempo); ?></span>
                            <input id="tempo_sec_rest_<?= $index ?>" type="hidden" value="<?= $tempo ?>">
                        </p>
                        <button href="Pousada/pousada_cancelar_recuperacao.php?cod=<?= $pers["cod"]; ?>"
                                data-question="Deseja cancelar este tratamento?"
                                class="link_confirm btn btn-danger">
                            Cancelar
                        </button>
                        <button href="Vip/hospital_finalizar.php?cod=<?= $pers["cod"]; ?>"
                                data-question="Deseja finalizar este tratamento agora?"
                                class="link_confirm btn btn-info"
                            <?= $userDetails->conta["gold"] < PRECO_GOLD_FINALIZAR_TRATAMENTO_HOSPITAL ? "disabled" : "" ?>>
                            <?= PRECO_GOLD_FINALIZAR_TRATAMENTO_HOSPITAL ?> <img src="Imagens/Icones/Gold.png"/>
                            Finalizar imediatamente
                        </button>
                        <button href="VipDobroes/hospital_finalizar.php?cod=<?= $pers["cod"]; ?>"
                                data-question="Deseja finalizar este tratamento agora?"
                                class="link_confirm btn btn-info"
                            <?= $userDetails->conta["dobroes"] < PRECO_DOBRAO_FINALIZAR_TRATAMENTO_HOSPITAL ? "disabled" : "" ?>>
                            <?= PRECO_DOBRAO_FINALIZAR_TRATAMENTO_HOSPITAL ?> <img src="Imagens/Icones/Dobrao.png"/>
                            Finalizar imediatamente
                        </button>
                    <? else : ?>
                        <button href="link_Pousada/pousada_finalizar_recuperacao.php?cod=<?= $pers["cod"]; ?>"
                                class="link_send btn btn-success">
                            Finalizar tratamento
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php render_personagem_sub_panel_with_img_bottom(); ?>
            <?php render_personagem_panel_bottom() ?>
        <?php endforeach; ?>
    </div>
</div>
