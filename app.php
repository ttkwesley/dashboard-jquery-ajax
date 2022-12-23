<?php
//Classe dashboardd
use Conexao as GlobalConexao;

class Dashboard
{
    //atributos do dashboard
    public $data_inicio;
    public $data_fim;
    public $numeroVendas;
    public $totalVendas;
    public $clientesAtivos;
    public $clientesInativos;
    public $totalDeReclamação;
    public $totalDeElogio;
    public $totalDeSujestao;
    public $totalDeDespesas;
    //metodos

    //Recuperar
    public function __get($atributo)
    {
        return $this->$atributo;
    }
    //Setar
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
        return $this;
    }
}

//Classe conexão com o banco de dados 
class Conexao
{
    //metodos
    private $host = 'localhost'; //host do server
    private $dbname = 'dashboard'; //nome do banco de dados
    private $usuario = 'root'; //usuario do banco de dados
    private $senha = ''; //senha do banco de dados
    //atributos
    public function conectar()
    {
        try { //Testando a conexao 
            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname", //Define o banco de dados como mysql
                "$this->usuario", //usuario faz o retorno direto do objeto
                "$this->senha" //senha faz o retorno direto do objeto
            );
            $conexao->exec('set charset utf8'); //Modelo de caractere
            return $conexao;
        } catch (PDOException $e) { //tratamento do erro caso a conexao falhe p
            echo '<p>' . $e->getMessage() . '</p>';
        }
    }
}

//Manipulação do objeto no banco de dados 
class Bd
{

    //atributos
    private $conexao;
    private $dashboard;
    //metodos
    public function __construct(Conexao $conexao, Dashboard $dashboard) //Tipando os atributos com os objetos criado nos metodos a cima
    {
        $this->conexao = $conexao->conectar();
        $this->dashboard = $dashboard;
    }

    //Recuperar numero de vendas
    public function getNumeroVendas()
    {
        $query = '
        SELECT 
            COUNT(*) AS numero_vendas 
        FROM 
            tb_vendas 
        WHERE
            data_venda  BETWEEN :data_inicio and :data_fim';
        //Statement
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
    }
    //Recuperar total de vendas
    public function getTotalVendas()
    {
        $query = '
        SELECT 
            SUM(total) AS total_vendas 
        FROM 
            tb_vendas 
        WHERE
            data_venda  BETWEEN :data_inicio and :data_fim';
        //Statement
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }
    //Recuperar Clientes ativos
    public function getClientesAtivos()
    {
        $query = '
        SELECT 
            COUNT(*) as clientes_ativos 
        FROM 
            tb_clientes 
        WHERE
            cliente_ativo = 1 ';
        //Statement
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
    }
    //Recuperar clientes inativos
    public function getClientesInativos()
    {
        $query = '
        SELECT 
            COUNT(*) as clientes_inativos
        FROM 
            tb_clientes 
        WHERE
            cliente_ativo = 0 ';
        //Statement
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
    }
    //Total de reclamação
    public function getTotalDeReclamaçoes()
    {
        $query = '
        SELECT 
            COUNT(*) as clientes_reclamacao
        FROM 
            tb_contatos
        WHERE tipo_contato = 1 ';
        //Statement
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_reclamacao;
    }
    public function getTotalDeElogios()
    {
        $query = '
        SELECT 
            COUNT(*) as clientes_elogio
        FROM 
            tb_contatos
        WHERE tipo_contato = 3 ';
        //Statement
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_elogio;
    }
    public function getTotalDeSujestao()
    {
        $query = '
        SELECT 
            COUNT(*) as clientes_sujestao
        FROM 
            tb_contatos
        WHERE tipo_contato = 2 ';
        //Statement
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_sujestao;
    }
    public function getTotalDespesas()
    {
        $query = '
        SELECT 
            SUM(total) AS total_despesas
        FROM 
            tb_despesas 
        WHERE
            data_despesa  BETWEEN :data_inicio and :data_fim';
        //Statement
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
    }
}

//Logica do script 
$dashboard = new Dashboard();
$conexao = new Conexao();

$competencia = explode('-', $_GET['competencia']); //Separar o ano do mes atraves do explode 
$ano = $competencia[0]; //O valor do ano está guardado na chave 0 de competencia
$mes = $competencia[1]; //O valor do ano está guardado na chave 1 de competencia
$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano); //Recuperação de quantos dias tem o mes 


$dashboard->__set('data_inicio', $ano . '-' . $mes . '-' . '-01'); //Fomato passado de maneira formatada para o banco de dados que utiliza como separação de data -
$dashboard->__set('data_fim', $ano . '-' . $mes . '-' . $dias_do_mes);


$bd = new Bd($conexao, $dashboard);

//Numero de vendas
$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
//Total de vendas
$dashboard->__set('totalVendas', $bd->getTotalVendas());
//Clientes Ativos
$dashboard->__set('clientesAtivos', $bd->getClientesAtivos());
//Clientes Inativos
$dashboard->__set('clientesInativos', $bd->getClientesInativos());
//Total de reclamação 
$dashboard->__set('totalDeReclamacao', $bd->getTotalDeReclamaçoes());
//Total de elogios
$dashboard->__set('totalDeElogio', $bd->getTotalDeElogios());
//Total de Sugestão
$dashboard->__set('totalDeSujestao', $bd->getTotalDeSujestao());
//Total de despesas
$dashboard->__set('totalDeDespesas', $bd->getTotalDespesas());

echo json_encode($dashboard); //Faz a transcrição do objeto para o formato json
