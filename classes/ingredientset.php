<?php

/*
 * rbook Recipe Management System
 * Copyright (C) 2005 Andrew Violette andrew@andrewviolette.net
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/*
 * $Id: ingredientset.php,v 1.11 2006/11/10 01:58:42 aviolette Exp $
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: ingredientset.php,v 1.11 2006/11/10 01:58:42 aviolette Exp $
 */

class IngredientSet extends BaseRecord {

    var $name;
    var $rows;
    var $id;

    function __construct() {
        $this->BaseRecord();
        $this->name = "Ingredients";
        $this->rows = array();
        $this->id = "c" . microtime();
    }

    function saveSet(&$db, $recipe, $orderid) {
        $this->dbCreateNew($db, $recipe, $orderid);
    }

    function updateDb(&$db, $recipe, $orderId) {
        $this->runQuery($db, "update ingredientsets set name = ? where id = ?",
                    array($this->name, $this->id));

	    $this->runQuery($db, "delete from ingredients where setid = ?",
                    array($this->id));

        $this->writeIngredients($db);
    }

    function dbCreateNew(&$db, $recipe, $orderid) {
        $id = $db->nextId("ingredientsets");
        $this->runQuery($db, "INSERT INTO ingredientsets (id, name, recipeid, orderid) VALUES (?, ?, ?, ?)",
               array($id, $this->name, $recipe->id, $orderid));
        $this->id = $id;
        $this->writeIngredients($db);

        return true;
    }

    function writeIngredients(&$db) {
        $query = "INSERT INTO ingredients (id, setid, amount, description, orderid) values (?, ?, ?, ?, ?)";
        $statement =& $db->prepare($query);
        for($i = 0; $i < count($this->rows); $i++) {
            $ing = $this->rows[$i];
            $iid = $db->nextId("ingredients");
            $values = array($iid, $this->id, $ing->amount, $ing->description, $ing->order);
            $rs = $db->execute($statement, $values);
			if(PEAR::isError($rs)) {
                if(defined("DEBUG_SQL")) {
                    error_log("Query: " . $query . ", values: " . implode(":", $values));
                }
			    die($rs->getMessage());
            }
        }
    }

    function delete() {
	    $is_id = array('id' => $this->id);
	    $table = 'ingredientset';
        BaseRecord::deleteMultipleOfClass($is_id, $table);
    }

    function to_s() {
        $buf = "Name: " . $this->name . "\n";
        for($i=0; $i < count($this->rows); $i++) {
            $ingredient = $this->rows[$i];
            $buf = $buf . $ingredient->amount . " => " . $ingredient->description . "\n";
        }
        return $buf;
    }

    function update($postValues) {
        $this->rows = array();
        $this->name = $_POST['name' . $this->id];
        foreach($postValues as $key => $value) {
            if(preg_match('/^desc-(\S+)-(\d+)/', $key, $match)) {
                if($match[1] != $this->id) {
                    continue;
                }
                $values[$match[2]] = $this->unescapeLiteral($value);
            }
        }
        foreach($postValues as $key => $value) {
            if(preg_match('/^amount-(\S+)-(\d+)/', $key, $match)) {
                if($match[1] != $this->id) {
                    continue;
                }
                $ingredient = new Ingredient();
                $ingredient->amount = $value;
                $ingredient->description = $values[$match[2]];
                $ingredient->order = $match[2];
                $this->rows[] = $ingredient;
            }
        }
    }
}
?>
