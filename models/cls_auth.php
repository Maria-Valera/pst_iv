<?php
  if(!class_exists("cls_db")) require("cls_db.php");

  class cls_auth extends cls_db{
    private $cedula_usuario, $cedula, $clave_usuario, $pregunta1, $pregunta2, $pregunta3, $respuesta1, $respuesta2, $respuesta3;

    public function __construct(){
      parent::__construct();
      $this->cedula_usuario = null;
      $this->cedula = null;
      $this->clave_usuario = null;
      $this->pregunta1 = null;
      $this->pregunta2 = null;
      $this->respuesta1 = null;
      $this->respuesta2 = null;
    }
    
    public function SetDatos($d){
      $this->cedula_usuario = isset($d['cedula_usuario']) ? $d['cedula_usuario'] : null;
      $this->clave_usuario = isset($d['clave_usuario']) ? $d['clave_usuario'] : null;
      $this->pregunta1 = isset($d['pregunta1']) ? $d['pregunta1'] : null;
      $this->pregunta2 = isset($d['pregunta2']) ? $d['pregunta2'] : null;
      $this->pregunta3 = isset($d['pregunta3']) ? $d['pregunta3'] : null;
      $this->respuesta1 = isset($d['respuesta1']) ? strtoupper($d['respuesta1']) : null;
      $this->respuesta2 = isset($d['respuesta2']) ? strtoupper($d['respuesta2']) : null;
      $this->respuesta3 = isset($d['respuesta3']) ? strtoupper($d['respuesta3']) : null;
    }
    
    public function Login(){
      $res = $this->Query("SELECT * FROM usuario WHERE cedula_usuario = '$this->cedula_usuario' ;");
      
      if($res->num_rows > 0){
        $datos = $this->Get_array($res);
        if($datos['estatus_usuario'] === "0") return [false,"err/06AUTH"];
        
        if(password_verify($this->clave_usuario, $datos['clave_usuario'])){
          session_start();
          $_SESSION['cedula'] = $datos['cedula_usuario'];
          $_SESSION['username'] = $datos['nombre_usuario'];
          $_SESSION['permisos'] = $datos['permiso_usuaio'];
          $_SESSION['nom_rol'] = $datos['tipo_usuario'];
          
          return [true,'msg/01AUTH'];
        }
        // if($datos['id_rol'] != 1) $this->intentos($datos['id_user']);
        return [false,'err/07AUTH'];
      }
      else return [false,"err/05AUTH"];
      
    }

    public function intentos($id_user){
      if(!isset($_COOKIE['trying_pw'])){
        setcookie("trying_pw",1,(time()+3600));
      }else{
        $pw = intval($_COOKIE["trying_pw"]);
        if($pw == 2){
          setcookie("trying_pw","",(time()-3600));
          $this->query("UPDATE usuarios SET status_user = 0 WHERE id_user = $id_user");
          return [false, "err/09AUTH"];
        }
        setcookie("trying_pw",2,(time()+3600));
      }
    }

    public function Register_user(){
      // Comprobamos que la persona ya este registrada en la base de datos
      $res = $this->Query("SELECT id_person,if_user FROM personas WHERE cedula_person = $this->cedula;");

      if($res->num_rows > 0){
        // Comprobamos que la persona tenga permisos para tener usuario
        $datos = $this->Get_array($res);
        if($datos['if_user'] == "0") return "err/03AUTH";

        // Comprobamos si la persona ya posee un usuario
        $res = $this->Query("SELECT * FROM usuarios WHERE person_id_user = ".$datos['id_person']." ;");
        if($res->num_rows > 0) return "err/04AUTH";

        $this->password = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]);

        $sql_insert = "INSERT INTO usuarios(id_user,person_id_user,password_user,status_user,id_rol,
        created_user,pregun1_user,pregun2_user,respuesta1_user,respuesta2_user) 
        VALUES(null,".$datos['id_person'].",'$this->password',1,3,NOW(),$this->pregunta1,$this->pregunta2,'$this->respuesta1','$this->respuesta2');";
            
        $this->Query($sql_insert);

        if($this->Result_last_query()) return "msg/02AUTH";
        else return "err/05AUTH";
      }else{
        return "err/02AUTH";
      }
    }

    public function Get_me($id_user){
      $sql = "SELECT 
        personas.id_person,
        personas.cedula_person,
        personas.tipo_person,
        personas.nom_person,
        personas.telefono_movil_person,
        personas.telefono_casa_person,
        personas.direccion_person,
        personas.correo_person,
        usuarios.id_user,
        usuarios.pregun1_user,
        usuarios.pregun2_user,
        usuarios.respuesta1_user,
        usuarios.respuesta2_user
        FROM personas INNER JOIN usuarios ON personas.id_person = usuarios.person_id_user WHERE usuarios.id_user = $id_user";
      $result = $this->Query($sql); 
      $datos = $this->Get_array($result);
      return $datos;
    }

    public function FindUser($cod){
      $cod = $this->Clean(intval($cod));

      $sql = "SELECT personas.id_person,personas.if_user FROM personas WHERE personas.cedula_person = $cod ;";
      $result = $this->Query($sql); 

      if($result->num_rows > 0){
        $datos = $this->Get_array($result);

        if($datos['if_user'] == '0'){
          return [
            'status' => false,
            'next' => 1,
            'message' => [
              'code' => 'error',
              'msg' => "Usted no esta habilidato para poseer un usuario",
            ],
          ];
        }

        $sql2 = "SELECT usuarios.id_user,usuarios.pregun1_user,usuarios.pregun2_user FROM usuarios WHERE usuarios.person_id_user = ".$datos['id_person'];
        $result_2 = $this->Query($sql2);

        if($result_2->num_rows == 0){
          return [
            'status' => true,
            'cedula' => $cod,
            'id' => $datos['id_person'],
            'next' => 2,
            'message' => [
              'code' => 'success',
              'msg' => "Puedes proceder a crear tu usuario, con tu clave, preguntas y respuestas de seguridad",
            ],
            'view' => 'sign_in'
          ];
        }

        $datos_2 = $this->Get_array($result_2);
        $pg1 = $this->Get_Preguntas($datos_2['pregun1_user']);
        $pg2 = $this->Get_Preguntas($datos_2['pregun2_user']);

        return [
          'cedula' => $cod,
          'id' => $datos_2['id_user'],
          'pregun1' => $pg1,
          'pregun2' => $pg2,
          // 'respues1' => $rp1,
          // 'respues2' => $rp2,
          'status' => true,
          'next' => 2,
          'view' => 'recuperar_clave'
        ];
      }

      return [
        'status' => false,
        'next' => 1,
        'message' => [
          'code' => 'error',
          'msg' => "Su cédula no se encuentra registrada",
        ]
      ];      
    }
    public function ValidarRespuestas($array){
      $rp1 = $this->Clean($array['respuesta1']);
      $rp2 = $this->Clean($array['respuesta2']);
      $id = $this->Clean(intval($array['user_id']));

      $sql = "SELECT person_id_user FROM usuarios WHERE respuesta1_user = '$rp1' AND respuesta2_user = '$rp2' AND id_user = $id;";
      $result = $this->Query($sql);

      if($result->num_rows > 0){
        return [
          'cedula' => $array['cedula'],
          'id' => $id,
          'next' => 3,
          'status' => true,
        ];
      }

      return [
        'cedula' => $array['cedula'],
        'id' => $id,
        'next' => 2,
        'status' => false,
        'message' => [
          'code' => 'error',
          'msg' => "Las respuestas no son correctas",
        ]
      ];
    }

    public function resetPassword($array){
      $password = password_hash($array['password'], PASSWORD_BCRYPT, ['cost' => 12]);
      $id = $this->Clean(intval($array['user_id']));

      $sql = "UPDATE usuarios SET status_user = 1,password_user = '$password' WHERE id_user = $id ;";
      $this->Query($sql);

      if($this->Result_last_query()){
        return [
          'status' => true,
          'next' => 1,
          'message' => [
            'code' => 'success',
            'msg' => "Su clave a sido actualizada",
          ]
        ];
      }

      return [
        'status' => false,
        'next' => 1,
        'message' => [
          'code' => 'error',
          'msg' => "Su clave no a sido actualizada"
        ]
      ];
    }

    public function Get_Preguntas($id = ""){
      if($id == "") $sql = "SELECT * FROM preguntas ;"; else $sql = "SELECT * FROM preguntas WHERE id_pregun = $id ;";
      if($id == "") return $this->Get_todos_array($this->Query($sql)); else return $this->Get_array($this->Query($sql));
    }

    public function VerificarCorreo($cedula, $email){
      $email = strtoupper($email);
      $sql = "SELECT usuarios.id_user FROM personas 
        INNER JOIN usuarios ON usuarios.person_id_user = personas.id_person WHERE 
        personas.cedula_person = '$cedula' AND personas.correo_person = '$email';";
      
      $result = $this->Query($sql);
      if($result->num_rows > 0){
        $datos = $this->Get_array($result);
        $code = $this->Make_code_recovery($datos['id_user']);
        $this->SendEmail($code, $email);

        return [
          'status' => true,
          'next' => 2,
          'id_user' => $datos['id_user'],
          'message' => [
            'code' => 'success',
            'msg' => "Correo verificado",
          ]
        ];
      }

      return [
        'status' => false,
        'next' => 1,
        'message' => [
          'code' => 'error',
          'msg' => "El correo ingresado no coincide",
        ]
      ];
    }

    public function Make_code_recovery($id_user){
      $code = $this->generateRandomString();

      $sql = "SELECT * FROM codigos_recuperacion WHERE char_code = '$code';";
      $result = $this->Query($sql);

      if($result->num_rows === 0){
        $datos = $this->Get_array($result);
        $this->Query("UPDATE codigos_recuperacion SET status_code = 0 WHERE id_user = $id_user;");

        $sql = "INSERT INTO codigos_recuperacion(date_cod, status_code, char_code, id_user) VALUES (NOW(),'1','$code',$id_user);";
        $this->Query($sql);

        return $code;
      }

      die("FALLO algo");
      $this->Make_code_recovery($id_user);
    }

    function generateRandomString($length = 8) { 
      return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
    } 

    public function ValidacionCodigo($datos){
      
      $code = $datos['code'];
      $id_user = $datos['id'];

      $sql = "SELECT * FROM codigos_recuperacion WHERE char_code = '$code' AND id_user = $id_user AND status_code = 1;";
      $result = $this->Query($sql);

      if($result->num_rows > 0){

        return [
          'status' => true,
          'next' => 3,
          'id_user' => $id_user,
          'message' => [
            'code' => 'success',
            'msg' => "Código verificado!",
          ]
        ];
      }
      return [
        'status' => false,
        'next' => 2,
        'id_user' => $id_user,
        'message' => [
          'code' => 'error',
          'msg' => "Código incorrecto o invalido",
        ]
      ];
    }

  }