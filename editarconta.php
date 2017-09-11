<html>   
    <head>   
        <TITLE>Editar conta</TITLE>   
        <meta charset="utf-8">
        <link rel="stylesheet" href="estilos.css" />
    </head>   
    <body>   
        <div id="contido">  
            <h1>EDITAR CONTA</h1>
            <?php
            session_start();
            require_once 'conectarPDO.php';
            try {
                //Establece conexión a BD
                $db = dbConnect();

                //Se foi premido o boton delete, figurará na variable global $_POST (nunca se executa a 1ª vez)
                if (array_key_exists('delete', $_POST)) {

                    // Definimos consulta para borrar a conta actual
                    $sql = 'DELETE FROM conta WHERE id = ?';

                    //Preparamos consulta. As consultas preparadas son o método mais indicado para evitar problemas derivados do SQL Injection
                    $stmt = $db->prepare($sql);
                    $resultado = $stmt->execute(array($_GET['id']));

                    //A execución da consulta devolve un booleano. Se for true, é sinal de que todo correu ben
                    if ($resultado) {
                        //Indica cantos rexistros foron actualizados (un libro de cada vez). 
                        echo '<p class="mensaxe">Eliminada a conta con identificador ' . $_GET['id'] . '</p>';
                    } else {
                        echo '<p class="mensaxe">Non fun capaz de eliminar a conta con identificador ' . $_GET['id'] . '</p>';
                    }
                    header("refresh: 4;url=infocontas.php?id=" . $_SESSION['id_sucursal']);
                    exit;
                }

                //Se foi premido o boton update, figurará na variable global $_POST (nunca se executa a 1ª vez)
                if (array_key_exists('update', $_POST)) {
                    //Definimos consulta de actualización con todos os campos da táboa que poden ser actualizados
                    $sql = "UPDATE conta SET id_tipoconta = ?, estado = ?, balance = ? "
                            . "WHERE id = ?";

                    //Preparamos consulta. As consultas preparadas son o método mais indicado para evitar problemas derivados do SQL Injection
                    $stmt = $db->prepare($sql);

                    //Controlamos valor do campo Balance
                    if (empty($_POST['balance'])) {
                        $balance = NULL;
                    } else {
                        $balance = $_POST['balance'];
                    }
                    //Executamos consulta e pasamos como parámetros os valores recollidos no formulario
                    $resultado = $stmt->execute(array($_POST['tipoconta'], $_POST['estado'], $balance,
                        $_GET['id']));

                    //A execución da consulta devolve un booleano. Se for true, é sinal de que todo correu ben
                    if ($resultado) {
                        //Indica cantos rexistros foron actualizados (deberia ser unha conta de cada vez). 
                        $count = $stmt->rowCount();
                        print("Actualizouse $count rexistro.\n");
                    } else {
                        echo '<p>Produciuse un erro na actualizacion..</p>';
                    }

                    //Despois de informar ao usuario recargamos a páxina. Tamén debemos liquidar o script con exit() ou die.
                    header("refresh: 4;editarconta.php?id=" . $_GET['id']);
                    exit;
                }

                if (isset($_GET['id'])) {

                    //Definimos consulta que devolve todos os datos relativos a unha conta
                    $sql = 'SELECT * FROM conta WHERE id=? ';
                    $stmt = $db->prepare($sql);

                    //Recuperamos resultado. Só devolve unha fila
                    $stmt->execute(array($_GET['id']));
                    $fila = $stmt->fetch();

                    //Controlamos o caso de que non devolva nada
                    if (empty($fila)) {
                        echo "Non se atoparon resultados !!";
                        header("refresh: 4;url=infosucursais.php");
                        exit;
                    }

                    //Definimos consulta que devolve combo con todos os TIPOS de conta e o TIPO da conta actual seleccionado
                    $tipoConta = "";
                    $sql2 = "SELECT * "
                            . "FROM tipoconta";

                    //Executamos consulta e almacenamos resultado nunha matriz
                    $stmt2 = $db->query($sql2);
                    $filasTipoConta = $stmt2->fetchAll();

                    //Percorremos matriz e vamos colocando no combo cada fila. Tipo Conta asociado á conta actual está seleccionado
                    foreach ($filasTipoConta as $filaTipoConta) {
                        $tipoConta.="<option value='" . $filaTipoConta['id'] . "'";
                        if (strcmp($filaTipoConta['id'], $fila['id_tipoconta']) == 0) {
                            $tipoConta.=" selected='selected' ";
                        }
                        $tipoConta.=">" . $filaTipoConta['nome'] . "</option>";
                    }

                    //Definimos consulta que devolve o nome do cliente nun campo de texto. ATENCIÓN!!!
                    $sql3 = "SELECT nomecompleto,tipocliente "
                            . "FROM cliente "
                            . "WHERE id =" . $fila['id_cliente'];
                    $stmt3 = $db->query($sql3);
                    $filaCliente = $stmt3->fetch();

                    //Definimos consulta que devolve combo con todos os ESTADOS de conta e o ESTADO actual seleccionado
                    $estado = "";
                    if ($fila['estado'] == 'aberta') {
                        $estado .="<option value='aberta' selected='selected'>aberta</option>"
                                . "<option value='pechada'>pechada</option>"
                                . "<option value='suspensa'>suspensa</option>";
                    } else if ($fila['estado'] == 'pechada') {
                        $estado .="<option value='aberta'>aberta</option>"
                                . "<option value='pechada' selected='selected'>pechada</option>"
                                . "<option value='suspensa'>suspensa</option>";
                    } else {
                        $estado .="<option value='aberta'>aberta</option>"
                                . "<option value='pechada'>pechada</option>"
                                . "<option value='suspensa' selected='selected'>suspensa</option>";
                    }
                } else {
                    echo 'Houbo un erro co identificador de sucursal';
                    header("refresh: 4;url=infosucursais.php");
                    exit;
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
                     <tr><td><a href="" >DATOS DA CONTA</a></td></tr>
                    <tr><td><label for="codigo">Código:</label></td>
                        <td><input type="number" name="codigo" id="codigo" disabled="disabled" value="<?php echo $fila['id'] ?>"><br/></td></tr>
                    <tr><td><label for="tipoconta">Tipo Conta:</label></td>
                        <td><select name="tipoconta" id="tipoconta"><?php echo $tipoConta; ?></select></td></tr>
                    <tr><td><label for="cliente">Cliente:</label></td>
                        <td><input type="text" name="cliente" id="cliente" disabled="disabled" value="<?php echo $filaCliente['nomecompleto']; ?>"><br/></td></tr>
                    <tr><td><label for="tipocliente">Tipo cliente:</label></td>
                        <td><input type name="tipocliente" id="tipocliente" disabled="disabled" value="<?php echo $filaCliente['tipocliente']; ?>"></td></tr>
                                 
            
                    <tr><td><label for="estado">Estado:</label></td>
                        <td><select name="estado" id="estado">
                                <?php echo $estado; ?>
                            </select>
                        </td></tr>
                    <tr><td><label for="balance">Balance:</label></td>
                        <td><input type="text" name="balance" id="balance" value=<?php echo $fila['balance'] ?>><br/></td></tr>
                    <tr><td colspan="2">
                            <button type="submit" name="update"> Actualizar conta</button>
                            <button type="submit" name="delete">Eliminar conta</button>
                            <button formaction="infocontas.php?id=<?php echo $_SESSION['id_sucursal'] ?>">Cancelar</button></td></tr>
                    </td></tr>
                </table>
            </form>
        </div>
    </body>
</html>