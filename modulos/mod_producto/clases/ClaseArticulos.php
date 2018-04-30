<?php

/*
 * @Copyright 2018, Alagoro Software. 
 * @licencia   GNU General Public License version 2 or later; see LICENSE.txt
 * @Autor Alberto Lago Rodríguez. Alagoro. alberto arroba alagoro punto com
 * @Descripción	
 */

include_once $RutaServidor . $HostNombre . '/modulos/claseModelo.php';

/**
 * Description of ClaseArticulos
 *
 * @author alagoro
 */
class alArticulos extends Modelo { // hereda de clase modelo. Hay una clase articulos que hizo Ricardo & Co.

    public function leer($idArticulo) {
        $sql = 'SELECT *'
                . 'FROM articulos '
                . ' WHERE idArticulo =' . $idArticulo
                . ' LIMIT 1';
        return $this->consulta($sql);
    }

    public function leerPrecio($idArticulo, $idTienda = 1) {
        $sql = 'SELECT pre.* '
                . ', art.iva as ivaArticulo '
                . ', art.articulo_name as descripcion '
                . 'FROM articulosPrecios AS pre '
                . ' LEFT OUTER JOIN articulos AS art ON (art.idArticulo=pre.idArticulo) '
                . ' WHERE pre.idArticulo =' . $idArticulo
                . ' AND pre.idTienda= ' . $idTienda
                . ' LIMIT 1';
        return $this->consulta($sql);
    }

    public function leerXCodBarras($codbarras, $idTienda = 1) {
        $sql = 'SELECT art.*, artcb.codBarras, artti.crefTienda as referencia '
                . 'FROM articulos AS art '
                . ' LEFT OUTER JOIN articulosCodigoBarras AS artcb ON (art.idArticulo=artcb.idArticulo) '
                . ' LEFT OUTER JOIN articulosTiendas AS artti ON (art.idArticulo=artti.idArticulo) '
                . ' WHERE artcb.codBarras =' . $codbarras
                . ' AND artti.idTienda= ' . $idTienda;
        return $this->consulta($sql);
    }

    public function contarLikeCodBarras($codbarras, $idTienda = 1) {
        $sql = 'SELECT count(art.idArticulo) as contador '
                . 'FROM articulos AS art '
                . ' LEFT OUTER JOIN articulosCodigoBarras AS artcb ON (art.idArticulo=artcb.idArticulo) '
                . ' LEFT OUTER JOIN articulosTiendas AS artti ON (art.idArticulo=artti.idArticulo) '
                . ' WHERE artcb.codBarras LIKE \'%' . $codbarras . '%\''
                . ' AND artti.idTienda= ' . $idTienda;
        $consulta = $this->consulta($sql);
        $resultado = false;
        if($consulta['datos']){
            $resultado = $consulta['datos'][0]['contador'];
        }
        return $resultado;
    }

    public function leerLikeCodBarras($codbarras,$pagina=0, $idTienda = 1) {
        $sql = 'SELECT art.*, artcb.codBarras, artti.crefTienda as referencia '
                . 'FROM articulos AS art '
                . ' LEFT OUTER JOIN articulosCodigoBarras AS artcb ON (art.idArticulo=artcb.idArticulo) '
                . ' LEFT OUTER JOIN articulosTiendas AS artti ON (art.idArticulo=artti.idArticulo) '
                . ' WHERE artcb.codBarras LIKE \'%' . $codbarras . '%\''
                . ' AND artti.idTienda= ' . $idTienda;
        if($pagina !== 0){
            $inicio = (($pagina-1) * ARTICULOS_MAXLINPAG)+1;
            $sql .= ' LIMIT '.$inicio.', '.ARTICULOS_MAXLINPAG;
        }
        return $this->consulta($sql);
    }

}
