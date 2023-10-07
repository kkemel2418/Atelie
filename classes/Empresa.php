<?php

 
echo (" tes---teee dentro de empresa.php 2 ");


die();

class Empresa {
    private $cnpj;
    private $razaoSocial;
    private $endereco;
    private $nomeResponsavel;
    private $email;
    private $telefone;
    private $created_at;
    private $updated_at;

    // Construtor
    public function __construct($cnpj, $razao_social, $endereco, $nomeResponsavel, $email, $telefone) {
        $this->cnpj = $cnpj;
        $this->razaoSocial = $razao_social;
        $this->endereco = $endereco;
        $this->nomeResponsavel = $nomeResponsavel;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function teste(){
        echo (" olaaaa"); 
    }


    public function empresa($cnpj, $razaoSocial, $endereco, $nomeResponsavel, $email, $telefone) {

        // Inclua a classe Database
        include_once 'Database.php';

        // Crie uma nova instância da classe Database
        $database = new Database();
        $conn = $database->getConnection();

        // Verifique se a conexão foi bem-sucedida
        if ($conn) {
            try {
                // Prepare a declaração de inserção
                $stmt = $conn->prepare("INSERT INTO empresas (cnpj, razao_social, endereco, nome_responsavel, email, telefone, created_at, updated_at) 
                                       VALUES (:cnpj, :razaoSocial, :endereco, :nomeResponsavel, :email, :telefone, :created_at, :updated_at)");

                // Bind os valores
                $stmt->bindParam(':cnpj', $cnpj);
                $stmt->bindParam(':razaoSocial', $razaoSocial);
                $stmt->bindParam(':endereco', $endereco);
                $stmt->bindParam(':nomeResponsavel', $nomeResponsavel);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':telefone', $telefone);

                // Obtém a data e hora atual
                $dataAtual = date('Y-m-d H:i:s');

                $stmt->bindParam(':created_at', $dataAtual);
                $stmt->bindParam(':updated_at', $dataAtual);

                // Execute a declaração
                if ($stmt->execute()) {
                    return true; // Empresa cadastrada com sucesso
                } else {
                    return false; // Erro ao cadastrar a empresa
                }
            } catch (PDOException $e) {
                echo "Erro: " . $e->getMessage();
                return false; // Erro ao cadastrar a empresa
            }
        } else {
            echo "Erro na conexão com o banco de dados.";
            return false; // Erro na conexão com o banco de dados
        }
    }

    // Métodos GET e SET para os atributos
    public function getCnpj() {
        return $this->cnpj;
    }

    public function setCnpj($cnpj) {
        $this->cnpj = $cnpj;
    }

    // Repita os métodos GET e SET para os outros atributos (razaoSocial, endereco, nomeResponsavel, email, telefone, created_at, updated_at)

    // Método para atualizar a empresa
    public function updateEmpresa($razaoSocial, $endereco, $nomeResponsavel, $email, $telefone) {
        $this->razaoSocial = $razaoSocial;
        $this->endereco = $endereco;
        $this->nomeResponsavel = $nomeResponsavel;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function deleteEmpresa($cnpj) {
        // Inclua a classe Database
        include_once 'Database.php';

        // Crie uma nova instância da classe Database
        $database = new Database();
        $conn = $database->getConnection();

        // Verifique se a conexão foi bem-sucedida
        if ($conn) {
            try {
                // Prepare a declaração de exclusão
                $stmt = $conn->prepare("DELETE FROM empresas WHERE cnpj = :cnpj");

                // Bind o valor do CNPJ
                $stmt->bindParam(':cnpj', $cnpj);

                // Execute a declaração
                if ($stmt->execute()) {
                    return true; // Empresa excluída com sucesso
                } else {
                    return false; // Erro ao excluir a empresa
                }
            } catch (PDOException $e) {
                echo "Erro: " . $e->getMessage();
                return false; // Erro ao excluir a empresa
            }
        } else {
            echo "Erro na conexão com o banco de dados.";
            return false; // Erro na conexão com o banco de dados
        }
    }
}
