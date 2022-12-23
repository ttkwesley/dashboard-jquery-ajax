//Metodo para fazer a execução dos scripts apenas depois que o document for carregado
$(document).ready(() => {
    //Substituição do conteudo exibido na index atraves do atalho load do jquery que faz a requisição xmlhttp
    $('#documentacao').on('click', () => {
        $('#pagina').load('documentacao.html')
    })
    $('#suporte').on('click', () => {
        $('#pagina').load('suporte.html')
    })



    //Requisição ajax
    $('#competencia').on('change', e => { //Metodo de requisição assincrona 

        let competencia = $(e.target).val() // guarda o valor retornado dentro da variavel competencia

        //Método, url, dados, sucesso, erro 
        $.ajax({
            type: 'GET', //Metodos
            url: 'app.php', //Url
            data: `competencia=${competencia}`, //dados  // parametro ecaminhado por defaut = x-www-form-urlencoded
            dataType: 'json',
            success: dados => {  //sucesso 
                $('#numeroVendas').html(dados.numeroVendas)
                $('#totalVendas').html(dados.totalVendas)
                $('#clientesAtivos').html(dados.clientesAtivos)
                $('#clientesInativo').html(dados.clientesInativos)
                $('#totalReclamacao').html(dados.totalDeReclamacao)
                $('#totalElogio').html(dados.totalDeElogio)
                $('#totalSugestao').html(dados.totalDeSujestao)
                $('#totalDespesa').html(dados.totalDeDespesas)
            },
            error: erro => { console.log(erro) } //erro
        })
    })
})

