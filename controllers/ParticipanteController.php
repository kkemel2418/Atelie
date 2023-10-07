<?php

class ParticipanteController {

    public function index() {

        include_once 'Database.php';
    
        $database = new Database();
        $conn = $database->getConnection();
    
        if ($conn) {
            try {
             
                $stmt = $conn->prepare("SELECT * FROM participantes");
                $stmt->execute();
    
                $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($participantes) {
                    
                    header('Content-Type: application/json');
                    echo json_encode($participantes);
                } else {
                    
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(array('message' => 'Nenhum participante encontrado.'));
                }
    
            } catch (PDOException $e) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados.'));
        }
    }


    public function show($id) {
        include_once 'Database.php';

        $database = new Database();
        $conn = $database->getConnection();

        if ($conn) {
            try {
                $stmt = $conn->prepare("SELECT * FROM participantes WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $participante = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($participante) {
                    header('Content-Type: application/json');
                    echo json_encode($participante);
                } else {
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(array('message' => 'Participante não encontrado.'));
                }

            } catch (PDOException $e) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados.'));
        }
    }

    public function create() {
     
        $requestData = json_decode(file_get_contents('php://input'), true);
    

        if ($requestData && !empty($requestData['cpf']) && !empty($requestData['nome_completo'])
                         && !empty($requestData['email']) && !empty($requestData['campanha_id'])) {
    
            include_once 'Database.php';
    
            $database = new Database();
            $conn = $database->getConnection();

            if ($conn) {
                try {
                    if ($this->checkIfParticipanteExists($requestData['cpf'], $requestData['campanha_id'])) {
                        header('HTTP/1.1 409 Conflict');
                        echo json_encode(array('message' => 'Este participante já está cadastrado nesta campanha'));
                        return; 
                    }
    
                    $stmt = $conn->prepare("INSERT INTO participantes (cpf, nome_completo, email, campanha_id) 
                                           VALUES (:cpf, :nome_completo, :email, :campanha_id )");
    
                    $stmt->bindParam(':cpf', $requestData['cpf']);
                    $stmt->bindParam(':nome_completo', $requestData['nome_completo']);
                    $stmt->bindParam(':email', $requestData['email']);
                    $stmt->bindParam(':campanha_id', $requestData['campanha_id']);
    
                    if ($stmt->execute()) {
    
                        echo json_encode(array('message' => 'Participante cadastrado com sucesso'));
                    } else {
                    
                        echo json_encode(array('message' => 'Erro ao cadastrar o participante'));
                    }
                } catch (PDOException $e) {
            
                    echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
                }
            } else {
        
                echo json_encode(array('message' => 'Erro na conexão com o banco de dados'));
            }
    
        } else {
          
            echo json_encode(array('message' => 'Verifique se os dados estão completos'));
        }
    }
    
    public function update($id, $requestData) {
        include_once 'Database.php';

        $database = new Database();
        $conn = $database->getConnection();

        if ($conn) {
            try {
                $updateFields = '';

                foreach ($requestData as $field => $value) {
                    if ($field !== 'id') {
                        $updateFields .= "$field = :$field, ";
                    }
                }

                $updateFields = rtrim($updateFields, ', ');

                $stmt = $conn->prepare("UPDATE participantes SET $updateFields WHERE id = :id");

                foreach ($requestData as $field => $value) {
                    $stmt->bindParam(":$field", $value);
                }
                $stmt->bindParam(':id', $id);

                if ($stmt->execute()) {
                    echo json_encode(array('message' => 'Participante atualizado com sucesso'));
                } else {
                    echo json_encode(array('message' => 'Erro ao atualizar o participante'));
                }
            } catch (PDOException $e) {
                echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
            }
        } else {
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados'));
        }
    }
public function delete($id) {
    include_once 'Database.php';

    $database = new Database();
    $conn = $database->getConnection();

    if ($conn) {
        try {
            $stmt = $conn->prepare("DELETE FROM participantes WHERE id = :id");
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $response = array(
                    'success' => true,
                    'message' => 'Participante excluído com sucesso',
                    'deleted_id' => $id
                );
                echo json_encode($response);
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Erro ao excluir o participante'
                );
                echo json_encode($response);
            }
        } catch (PDOException $e) {
            $response = array(
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            );
            echo json_encode($response);
        }
    } else {
        $response = array(
            'success' => false,
            'message' => 'Erro na conexão com o banco de dados'
        );
        echo json_encode($response);
    }
}
    public function checkIfEmpresaExists($id) {
        include_once 'Database.php';
    
        $database = new Database();
        $conn = $database->getConnection();
    
        if ($conn) {
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM empresas WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
    
                $result = $stmt->fetchColumn();
    
                return $result > 0;
            } catch (PDOException $e) {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function checkIfParticipanteExists($cpf, $campanha_id) {
        include_once 'Database.php';
    
        $database = new Database();
        $conn = $database->getConnection();
    
        if ($conn) {
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM participantes WHERE cpf = :cpf AND campanha_id = :campanha_id");
                $stmt->bindParam(':cpf', $cpf);
                $stmt->bindParam(':campanha_id', $campanha_id);
                $stmt->execute();
    
                $result = $stmt->fetchColumn();
    
                return $result > 0;
            } catch (PDOException $e) {
            
                return false;
            }
        } else {
            return false;
        }
    }
    
    
}
