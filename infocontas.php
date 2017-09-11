<html>   
    <head>   
        <TITLE>Información Contas</TITLE>   
        <meta charset="utf-8">
        <link rel="stylesheet" href="estilos.css" />
    </head>   
    <body>   
        <div id="contido">  
            <h1>DATOS SUCURSAL</h1>
            <table> 
                <tr>    
                    <th>NOME SUCURSAL</th>    
                    <th>ENDEREZO COMPLETO</th>   
                    <th/>
                </tr>
                <?php
                session_start();
                require_once 'conectarPDO.php';
                try {
                    //Establece conexión a BD
                    $db = dbConnect();

                    if (isset($_GET['id'])) {
                        $_SESSION['id_sucursal'] = $_GET['id'];
                    }
                    //Definimos consulta SQL
                    if (!empty($_SESSION['id_sucursal'])) {
                        $sql = "SELECT id, nome, CONCAT (enderezo, ', ', cod_postal, ' ', cidade) as 'ENDEREZO COMPLETO' "
                                . "FROM sucursal "
                                . "WHERE id =?";

                        //Executamos consulta
                        $stmt = $db->prepare($sql);

                        //Recuperamos resultado. Só devolve unha fila
                        $stmt->execute(array($_SESSION['id_sucursal']));
                        $fila = $stmt->fetch();

                        echo '<tr>'
                        . '<td>' . $fila['nome'] . '</td>'
                        . '<td>' . $fila['ENDEREZO COMPLETO'] . '</td>'
                        . '</tr>';

                        echo '</table><br/><br/>
                             <h1>LISTADO CONTAS SUCURSAL</h1>
                            <div id="filtro">
                                <fieldset>
                    <legend>Filtrar por</legend>
                    <form action="" method="post">
                        <label for="titulo">Nome:</label>
                        <input type="text" name="titulo" id="titulo"/>

                        <label for="orde">Ordenar por:</label>
                        <select name="orde" id="orde">
                            <option value="nome" >Nome</option>
                            <option value="cod_postal">Cod. postal</option>
                            <option value="enderezo">Enderezo</option>
                        </select>
                        <input type="radio" name="senso" value="ASC" checked="checked">ASC</input>
                        <input type="radio" name="senso" value="DESC">DESC</input>
                        <button type="submit" name="filtrar">Filtrar</button>
                    </form>
                </fieldset>
            </div>
            <br/>  
                     
            <form action="" method="post">          
                <table> 
                    <tr>    
                        <th>CÓDIGO</th>    
                        <th>TIPO CONTA</th>   
                        <th>CLIENTE</th>
                        <th>TIPO CLIENTE</th>
                        <th>ESTADO</th>
                        <th>BALANCE</th>
                        <th/>
                    </tr>';

                        //Inicializamos variables para paxinacion
                        $numRexistrosPax = 1;
                        $paxina = 1;

                        //No caso de que esteamos noutra paxina diferente actualizamos valor $paxina
                        if (array_key_exists('pax', $_GET)) {
                            $paxina = $_GET['pax'];
                        }
                        //Definimos e executamos consulta para saber cantos rexistros van por páxina
                        $stmt = $db->query('SELECT COUNT(*) FROM conta WHERE id_sucursal=' . $_SESSION['id_sucursal']);
                        $totalRexistros = $stmt->fetchColumn();

                        $totalPaxinas = ceil($totalRexistros / $numRexistrosPax);

                        $sql2 = "SELECT c.id, t.nome, cl.nomecompleto, cl.tipocliente, c.estado, c.balance "
                                . "FROM conta c "
                                . "INNER JOIN tipoconta t ON c.id_tipoconta = t.id "
                                . "INNER JOIN cliente cl ON c.id_cliente = cl.id "
                                . "WHERE c.id_sucursal=" . $_SESSION['id_sucursal']
                                . " ORDER BY c.id "
                                . "LIMIT " . (($paxina - 1) * $numRexistrosPax) . ", $numRexistrosPax";

                        $stmt2 = $db->query($sql2);
                        $filas2 = $stmt2->fetchAll();

                        foreach ($filas2 as $fila2) {
                            echo '<tr>'
                            . '<td>' . $fila2['id'] . '</td>'
                            . '<td>' . $fila2['nome'] . '</td>'
                            . '<td>' . $fila2['nomecompleto'] . '</td>'
                            . '<td>' . $fila2['tipocliente'] . '</td>'
                            . '<td>' . $fila2['estado'] . '</td>'
                            . '<td>' . $fila2['balance'] . '</td>'
                            . '<td><a href=editarconta.php?id=' . $fila2['id'] . '>Editar contas</a></td>'
                            . '</tr>';
                        }

                        //Fechamos a conexión
                        $db = null;

                        echo "</table></br></br><div id='paxinado'>";
                        for ($i = 0; $i < $totalPaxinas; $i++) {
                            echo '<a href="infocontas.php?pax=' . ($i + 1) . '">' . ($i + 1) . '</a> | ';
                        }
                        echo "</div>";
                    } else {
                        echo 'Houbo un erro co identificador de sucursal';
                        header("refresh: 4;url=infosucursais.php");
                        exit;
                    }
                } catch (PDOException $e) {
                    print $e->getMessage();
                }
                ?>
                <tr><td colspan="4">
                        <button formaction="novaconta.php"> Nova conta</button>
                        <button formaction="infosucursais.php">Cancelar</button></td>
                </tr>
            </table>
        </form><br/><br/>
    </div>   
</body>   
</html>