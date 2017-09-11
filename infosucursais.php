<html>   
    <head>   
        <TITLE>Información Sucursais</TITLE>  
        <meta charset="utf-8">
        <link rel="stylesheet" href="estilos.css" />
    </head>   
    <body>   
        <div id="contido">  
            <h1>SUCURSAIS</h1>
            <div id="filtro">
                <fieldset>
                    <legend>Filtrar por</legend>
                    <form action="" method="post">
                        <label for="titulo">Nome:</label>
                        <input type="text" name="titulo" id="titulo"/>

                        <label for="orde">Ordenar por:</label>
                        <select name="orde" id="orde">
                            <option value='nome' >Nome</option>
                            <option value='cod_postal'>Cod. postal</option>
                            <option value='enderezo'>Enderezo</option>
                        </select>
                        <input type="radio" name="senso" value="ASC" checked="checked">ASC</input>
                        <input type="radio" name="senso" value="DESC">DESC</input>
                        <button type="submit" name="filtrar">Filtrar</button>
                    </form>
                </fieldset>
            </div>
            <br/> 
            <table> 
                <tr>    
                    <th>NOME SUCURSAL</th>    
                    <th>ENDEREZO COMPLETO</th>   
                    <th/>
                </tr>

                <?php
                require_once 'conectarPDO.php';
                try {
                //Inicializamos variables para paxinacion
                $numRexistrosPax = 5;
                $paxina = 1;

                //No caso de que esteamos noutra paxina diferente actualizamos valor $paxina
                if (array_key_exists('pax', $_GET)) {
                $paxina = $_GET['pax'];
                }

                //Establece conexión a BD
                $db = dbConnect();
                
                //Definimos e executamos consulta para saber cantos rexistros van por páxina
                $stmt = $db->query('SELECT COUNT(*) FROM sucursal');
                $totalRexistros = $stmt->fetchColumn();

                $totalPaxinas = ceil($totalRexistros / $numRexistrosPax);

                //Definimos consulta que recupera datos sobre sucursais, convenientemente paxinados
                $sql = "SELECT id, nome, CONCAT (enderezo, ', ', cod_postal, ' ', cidade) as 'ENDEREZO COMPLETO' "
                . "FROM sucursal ";


                /*    Filtramos polo nome da sucursal    */
                if (array_key_exists("filtrar", $_POST)) {
                $sql .= " WHERE nome like ?";
                $titulo = "%" . $_POST['titulo'] . "%";
                }
                /*                 * ***********      */

                //Ordena polo nome, cod postal ou enderezo da sucursal
                if (array_key_exists("orde", $_POST)) {
                $sql.=" ORDER BY " . $_POST['orde'] . ' ' . $_POST['senso'];
                } else
                $sql.=" ORDER BY nome";


                $sql .=" LIMIT " . (($paxina - 1) * $numRexistrosPax) . ", $numRexistrosPax";

                //mandamos a consulta da do segmento
                $stmt = $db->prepare($sql);
                $stmt->bindParam(1, $titulo, PDO::PARAM_STR);
                $stmt->execute();

                //Executamos consulta
                //Recuperamos resultado en forma de matriz
                $filas = $stmt->fetchAll();
                foreach ($filas as $fila) {
                echo '<tr>'
                . '<td>' . $fila['nome'] . '</td>'
                . '<td>' . $fila['ENDEREZO COMPLETO'] . '</td>'
                . '<td><a href=infocontas.php?id=' . $fila['id'] . '>Editar contas</a></td>'
                . '</tr>';
                }

                //Fechamos a conexión
                $db = null;

                echo "</table></br></br><div id='paxinado'>";
                for ($i = 0;
                $i < $totalPaxinas;
                $i++) {
                echo '<a href="infosucursais.php?pax=' . ($i + 1) . '">' . ($i + 1) . '</a> | ';
                }
                echo "</div>";
                } catch (PDOException $e) {
                print $e->getMessage();
                }
                ?>
        </div>
    <body>
</html>    