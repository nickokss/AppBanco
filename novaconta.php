<html>   
    <head>   
        <title>Nova conta</title>   
        <meta charset="utf-8">
        <link rel="stylesheet" href="estilos.css" />
    </head>   
    <body>   
        <div id="contido">  
            <h1>NOVA CONTA</h1>
            <?php
            session_start();
            require_once 'conectarPDO.php';
            try {
                //Establece conexión a BD
                $db = dbConnect();

                //Bloque de código para INSERIR novo rexistro
                if (array_key_exists('insert', $_POST)) {

                    //Definimos consulta de inserción
                    $sqlInsercion = "INSERT INTO conta "
                            . "VALUES (?,?,?,?,NOW(),null,'aberta',?)";

                    $stmtI = $db->prepare($sqlInsercion);
                    $resultado = $stmtI->execute(array($_POST['idconta'], $_POST['tipoconta'], $_SESSION['id_sucursal'], $_POST['cliente'], $_POST['balance']));

                    //A execución da consulta devolve un booleano. Se for true, é sinal de que todo correu ben
                    if ($resultado) {
                        //Informa que un novo rexistro foi inserido
                        echo '<p class="mensaxe">Inserida unha nova conta.</p>';
                    } else {
                        echo '<p class="mensaxe">Non fun capaz de inserir a nova conta </p>';
                    }
                    header("refresh: 4;url=infocontas.php?id=" . $_SESSION['id_sucursal']);
                    exit;
                }

                //Consulta para calcular novo identificador de conta
                $sql = "SELECT id "
                        . "FROM conta "
                        . "ORDER BY id DESC "
                        . "LIMIT 1";

                $stmt = $db->query($sql);
                $novoIdConta = $stmt->fetchColumn();
                $novoIdConta++;



                //Definimos consulta que devolve combo con todos os TIPOS de conta e o TIPO da conta actual seleccionado
                $comboTipoConta = "";
                $sqlComboTipoConta = "SELECT * "
                        . "FROM tipoconta";

                //Executamos consulta e almacenamos resultado nunha matriz
                $stmtTipoConta = $db->query($sqlComboTipoConta);
                $filasTipoConta = $stmtTipoConta->fetchAll();

                //Percorremos matriz e vamos colocando no combo cada fila. Tipo Conta asociado á conta actual está seleccionado
                foreach ($filasTipoConta as $filaTipoConta) {
                    $comboTipoConta.="<option value='" . $filaTipoConta['id'] . "'>" . $filaTipoConta['nome'] . "</option>";
                }

                //Consulta para mostrar nome sucursal
                $sqlSucursal = "SELECT nome FROM sucursal "
                        . "WHERE id=" . $_SESSION['id_sucursal'];

                $stmtS = $db->query($sqlSucursal);
                $sucursal = $stmtS->fetchColumn();


                //Consulta para cargar combocliente
                $comboCliente = "";
                $sqlComboCliente = "SELECT id,nomecompleto "
                        . "FROM cliente";

                //Executamos consulta e carregamos resultado nunha matriz
                $stmtCC = $db->query($sqlComboCliente);
                $filasCC = $stmtCC->fetchAll();

                //Percorremos matriz e colocamos resultados no combo
                foreach ($filasCC as $filaCC) {
                    $comboCliente.="<option value='" . $filaCC['id'] . "'>"
                            . $filaCC['nomecompleto'] . "</option>";
                }
            } catch (PDOException $e) {
                print $e->getMessage();
            }
            ?>

            <form action="" method="post">
                <table class="edicion">
                       
                    <!--Novo cliente-->
                  
                    <tr><td><a href="" >NOVO CLIENTE</a></td></tr>
                    <tr><td><label for="cliente">Cliente:</label></td>
                        <td><input type="text" name="cliente" id="cliente"  placeholder="Nome cliente" value="<?php echo $filaCliente['nomecompleto']; ?>"><br/></td></tr>
                    <tr><td><label for="tipocliente">Tipo cliente:</label></td>
                        <td> 
                        <select name="tipo_cli" id="tipo_cli">
                            <option value='autonomo' >autonomo</option>
                            <option value='entidade'>entidade</option>
                            <option value='persoal'>persoal</option>
                        </select></td></tr>
                     <tr><td colspan="2">
                            <button type="submit" name="update">Crear cliente</button>
                       </td></tr>
                     <br>                   
                     
                     <!-- **** -->
                     <tr><td><a href="" >DATOS NOVA CONTA</a></td></tr>
                     
                    <tr><td><label for="codigo">Identificador conta:</label></td>
                        <td><input type="number" name="idconta" id="idconta" value="<?php echo $novoIdConta ?>" readonly /><br/></td></tr>

                    <tr><td><label for="tipoconta">Tipo Conta:</label></td>
                        <td><select name="tipoconta" id="tipoconta"><?php echo $comboTipoConta; ?></select></td>
                    </tr>

                    <tr>
                        <td><label for="sucursal">Sucursal:</label></td>
                        <td><input type="text" name="sucursal" id="cliente" value="<?php echo $sucursal ?>" readonly /></td>
                    </tr>

                    <tr><td><label for="cliente">Cliente:</label></td>
                        <td>
                            <select name="cliente" id="cliente">
                                <?php echo $comboCliente; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="balance">Balance:</label></td>
                        <td><input type="text" name="balance" id="balance"></td>
                    </tr>
                    <tr><td colspan="2">
                            <button type="submit" name="insert">Alta de conta</button>
                            <button formaction="infocontas.php?id=<?php echo $_SESSION['id_sucursal'] ?>">Cancelar</button></td></tr>
                    </td></tr>
                </table>
            </form>
        </div>
    </body>
</html>