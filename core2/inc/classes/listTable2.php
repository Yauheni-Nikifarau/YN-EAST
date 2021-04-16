<?
require_once("class.ini.php");
class listTable2 extends initList {
	public $addSum 				= array();
	public $table_column			= array();
	public $table					= '';
	public $table_button			= array();
	public $table_search			= array();
	public $paintCondition			= array();
	public $paintColor			= array();
	public $fontColor			= array();
	public $fontWeight			= array();
	public $sqlSearch				= array();
	public $params					= array();
	public $data					= array();
	public $metadata					= array();
	public $noCheckboxes			= "no";
	public $main_table_id			= "";
	protected $resource				= "";
	public $search_table_id			= "";
	private $HTML					= "";
	public $SQL						= "";
	public $editURL					= "";
	public $addURL					= "";
	public $deleteURL				= "";
	public $deleteKey				= "";
	public $ajax					= 0;
	public $error					= "";
	private $customSearch			= array();
	private $customSearchHasVal		= false;
	private $columnSchema			= array();
	private $recordCount			= "";
	public $extOrder				= false;
	private $dataAlreadyGot			= false;
	private $extraHeaders			= array();
	public $roundRecordCount		= false;
	private $is_seq 				= false;

	public function __construct($name) {
		parent::__construct();

		$this->resource 		= $name;
		$this->main_table_id 	= "main_" . $name;
		$this->search_table_id 	= "search_" . $name;
	}
	
	public function button($value, $img = "", $onclick = "", $style = "", $type = 'button') {
		$id = uniqid();
		$out = '<input type="' . $type . '" class="button" value="' . $value . '" style="' . $style . '" onclick="' . ($onclick ? $onclick : "if(document.getElementById('$id') && document.getElementById('$id').form) document.getElementById('$id').form.onsubmit()") . '"/>';
		return $out;
	}
	
	public function setRecordCount($value) {
		$this->recordCount = $value;
	}
	
	private function commafy($_, $del = ';psbn&') {
	    return strrev( (string)preg_replace( '/(\d{3})(?=\d)(?!\d*\.)/', '$1' . $del , strrev( $_ ) ) );
	}
	
	function addColumn($name, $width = "0%", $type, $in = "", $processing = "", $sort = true) {
		$this->table_column[$this->main_table_id][] = array('name' => addslashes($name), 
															'type' => strtolower($type), 
															'in' => $in, 
															'width' => $width, 
															'processing' => $processing,
															'sort' => $sort);
	}

	/**
	 * Добавить новую кнопку
	 * @param $name
	 * @param $script
	 * @param string $msg
	 * @param int $nocheck
	 */
	function addButton($name, $script, $msg = "", $nocheck = 0) {
		$this->table_button[$this->main_table_id][] = array(
			'name' => addslashes($name),
			'url' => $script,
			'confirm' => addslashes($msg),
			'nocheck' => $nocheck
		);
	}
	
	public function addButtonCustom($html = '') {
		$this->table_button[$this->main_table_id][] = array('html' => $html);
	}

	/**
	 * Add search field
	 * @param $name - caption
	 * @param $field - destination field name
	 * @param $type - type of search field
	 * @param string $in - inner attributes
	 * @param string $out - outher html
	 * @return void
	 */
	public function addSearch($name, $field, $type, $in = "", $out = "") {
		$this->table_search[$this->main_table_id][] = array(
			'name' 		=> addslashes($name),
			'type' 		=> strtolower($type),
			'in' 		=> $in,
			'field' 	=> $field,
			'out' 		=> $out
		);
	}

	/**
	 * Get data array
	 * @throws Exception
	 * @return array
	 */
	public function getData() {
		
		// CHECK FOR SEARCH
		$ss = new Zend_Session_Namespace('Search');
		$ssi = $this->main_table_id;
		if (empty($ss->$ssi)) {
			$ss->$ssi = array();
		}
		$tmp = $ss->$ssi;
		$countPOST = 'count_' . $this->resource; //pagination record count
		$pagePOST = 'page_' . $this->resource; //pagination page number

		//CHECK RECORD COUNTER
		if (empty($_POST[$countPOST]) && empty($tmp[$countPOST])) {
			$_POST[$countPOST] = $this->recordsPerPage;
			$tmp[$countPOST] = $this->recordsPerPage;
		} elseif (empty($_POST[$countPOST]) && $tmp[$countPOST]) {
			$_POST[$countPOST] = $tmp[$countPOST];
		} else {
			$tmp[$countPOST] = $_POST[$countPOST];
		}
		
		//SEARCH
		if (!isset($_POST['search'])) $_POST['search'] = array();
		if (empty($_POST['search'][$this->main_table_id]) && empty($tmp['search'])) {
			$tmp['search'] = array();
		} elseif (empty($_POST['search'][$this->main_table_id]) && count($tmp['search'])) {
			$_POST['search'][$this->main_table_id] = $tmp['search'];
		} else {
			$tmp['search'] = $_POST['search'][$this->main_table_id];
		}
		if (!empty($_POST['clear_form' . $this->resource])) {
			$tmp['search'] = array();
			unset($_POST['search'][$this->main_table_id]);
		}

		//ORDERING
		if (!empty($_POST['orderField_' . $this->main_table_id])) {
			if (empty($tmp['order'])) {
				$tmp['order'] = $_POST['orderField_'.$this->main_table_id];
				$tmp['orderType'] = "asc";
			} else {
				if ($_POST['orderField_'.$this->main_table_id] == $tmp['order']) {
					if ($tmp['orderType'] == "asc") {
						$tmp['orderType'] = "desc";
					} elseif ($tmp['orderType'] == "desc") {
						$tmp['orderType'] = "";
						$tmp['order'] = "";
					} elseif ($tmp['orderType'] == "") {
						$tmp['orderType'] = "asc";
					}
				} else {
					$tmp['order'] = $_POST['orderField_' . $this->main_table_id];
					$tmp['orderType'] = "asc";
				}
			}
		}
		
		if (empty($_POST[$pagePOST])) {
			$_POST[$pagePOST] = 1;
		} else {
			$_POST[$pagePOST] = (int) $_POST[$pagePOST];
			if (!$_POST[$pagePOST]) $_POST[$pagePOST] = 1;
		}
		$ss->$ssi = $tmp;
		$search = "";
		$questions = array();


		if ($this->table) {
			$is = $this->db->fetchAll("EXPLAIN " . $this->table);
			$noauthor = true;
			foreach ($is as $value) {
				//проверака наличия поля автора
				if ($value['Field'] == 'author') {
					$noauthor = false;
				}
				//проверка наличия поля для последовательности
				if ($value['Field'] == 'seq') {
					$this->is_seq = true;
				}
			}

			if ($this->checkAcl($this->resource, 'list_owner') && !$this->checkAcl($this->resource, 'list_all')) {
				if ($noauthor) {
					throw new Exception("Данные не содержат признака автора!");
				} else {
					$auth 	= Zend_Registry::get('auth');
					$questions[] = $auth->NAME;
					$search = " AND author=?";
				}
			}
		}
		
		if (!empty($_POST['search'][$this->main_table_id]) && count($this->table_search)) {
			reset($this->table_search[$this->main_table_id]);
			$next = current($this->table_search[$this->main_table_id]);
			//echo "<PRE>";print_r($_POST['search']);echo "</PRE>";die;
			foreach ($_POST['search'][$this->main_table_id] as $search_value) {
				if (strpos($next['type'], '_custom') !== false) {
					$next['type'] = str_replace('_custom', '', $next['type']);
					$this->customSearch[$next['field']] = $search_value;
					if (trim($search_value)) $this->customSearchHasVal = true;
				} else {
					if ($next['type']) {
						if ($search_value != '' && $next['type'] != 'date') {
							if ($next['type'] == 'list' || $next['type'] == 'select') {
								if (strpos($next['field'], "ADD_SEARCH") === false) {
									$search .= " AND " . $next['field'] . "=?";
									$questions[] = $search_value;
								} else {
									$search .= " AND " . str_replace("ADD_SEARCH", $search_value, $next['field']);
								}
							} elseif ($next['type'] == 'checkbox' || $next['type'] == 'checkbox2') {
								if (strpos($next['field'], "ADD_SEARCH") === false) {
									
									$search .= " AND ({$next['field']}='" . implode("' OR {$next['field']}='", $search_value) . "')";
								} else {
									$search .= " AND " . str_replace("ADD_SEARCH", "?", $next['field']);
									$questions[] = $search_value;
								}
							} else {
								if (strpos($next['field'], "ADD_SEARCH") === false) {
									$search .= " AND " . $next['field']." LIKE ?";
									$questions[] = $search_value . "%";
								} else {
									$search .= " AND " . str_replace("ADD_SEARCH", $search_value, $next['field']);
								}
							}
						}
						if ($next['type'] == 'date') {
							if ($search_value[0] && !$search_value[1]) {
								$search .= " AND DATE_FORMAT({$next['field']}, '%Y-%m-%d') >= ?";
								$questions[] = $search_value[0];
							}
							if (!$search_value[0] && $search_value[1]) {
								$search .= " AND DATE_FORMAT({$next['field']}, '%Y-%m-%d') <= ?";
								$questions[] = $search_value[1];
							}
							if ($search_value[0] && $search_value[1]) {
								$search .= " AND DATE_FORMAT({$next['field']}, '%Y-%m-%d') BETWEEN ? AND ?";
								$questions[] = $search_value[0];
								$questions[] = $search_value[1];
							}
						}
					}
				}
				$next = next($this->table_search[$this->main_table_id]);
			}
		}
		
		
		$idm = substr($this->SQL, strripos($this->SQL, "SELECT ") + 6); 
		$idm = trim($idm);
		$idm = substr($idm, 0, strpos($idm, ","));		
		if (preg_match("/(as|AS)/", $idm, $as)) {			
			$idm = substr($idm, 0, strpos($idm, $as[0]));				
		}									
		if (preg_match("/(\[ON|OFF)\(([a-z._]+)\)\]/", $this->SQL, $mas)) {			
			$res = explode(".", $mas[2]);			
			if (isset($res[1])) {
				$str_repl_on = "'[ON(" .$res[0].".".$res[1].")]'";
				$str_repl_off = "'[OFF(" .$res[0].".".$res[1].")]'";	
				$table_data = $mas[2];					
			} else {				
				$str_repl_on = "'[ON(" .$res[0].")]'";
				$str_repl_off = "'[OFF(" .$res[0].")]'";
				$table_data = $res[0]."."."is_active_sw";	
			}						
			$this->SQL = str_replace($str_repl_on, "CONCAT_WS('','<img src=\"core2/html/".THEME."/img/on.png\" alt=\"on\" onclick=\"blockList.switch_active(this, event)\" t_name = ".$table_data.".', ".$idm.",'>')", $this->SQL);
			$this->SQL = str_replace($str_repl_off, "CONCAT_WS('','<img src=\"core2/html/".THEME."/img/off.png\" alt=\"off\" onclick=\"blockList.switch_active(this, event)\" t_name = ".$table_data.".', ".$idm.",'>')", $this->SQL);
		} else {
			$this->SQL = str_replace("[ON]", "<img src=\"core2/html/".THEME."/img/on.png\" alt=\"on\" />", $this->SQL);
			$this->SQL = str_replace("[OFF]", "<img src=\"core2/html/".THEME."/img/off.png\" alt=\"off\" />", $this->SQL);
		}		
		
		$this->SQL = str_replace("ADD_SEARCH", $search, $this->SQL);		
		$order = isset($tmp['order']) ? $tmp['order'] : '';
		if (isset($this->table_column[$this->main_table_id])) {
			foreach ($this->table_column[$this->main_table_id] as $seq => $columns) {
				if ($columns['type'] == 'function' && $order && $order == $seq + 1) {
					$this->extOrder = true;
					break;
				};
			}
		}
		if (!$this->extOrder && !$this->customSearchHasVal) {
			if ($order) {
				$tempSQL 		= $this->SQL;
				$check 			= explode("ORDER BY", $tempSQL);
				$lastPart 		= end($check);
				$orderField 	= $order + 1;
				
				if (count($check) > 1 && !empty($lastPart) && strpos($lastPart, 'FROM ') === false) {
					$tempSQL = "";
					$co = count($check);
					for ($i = 0; $i <= $co - 2; $i++) {
						$tempSQL .= $check[$i];
						if ($i < $co - 2) $tempSQL .= " ORDER BY ";
					}
					$this->SQL = $tempSQL . " ORDER BY " . $orderField . " " . $tmp['orderType'];
				}
				$this->SQL = $tempSQL . " ORDER BY " . $orderField . " " . $tmp['orderType'];
			}
			
			if ($_POST[$pagePOST] == 1) {
				$this->SQL .= " LIMIT " . $_POST[$countPOST];
			} else if ($_POST[$pagePOST] > 1){
				$this->SQL .= " LIMIT " . ($_POST[$pagePOST] - 1) * $_POST[$countPOST] . "," . $_POST[$countPOST];
			}
		}
		//echo "<textarea>$this->SQL</textarea>";//die();
		//echo "<PRE>";print_r($questions);echo "</PRE>";//die;
		//preg_match_all("/(?<=^|\))[^\)\(]+?(?=\(|$)/gim", $this->SQL, $tt, PREG_OFFSET_CAPTURE);

		if ($this->roundRecordCount) {
			$expl = $this->db->fetchAll('EXPLAIN ' . $this->SQL, $questions);
			$this->recordCount = 0;
			foreach ($expl as $value) {
				if ($value['rows'] > $this->recordCount) {
					$this->recordCount = $value['rows'];
				};
			}
			$res = $this->db->fetchAll($this->SQL, $questions);
		} else {
			if ($this->config->database->adapter == 'Pdo_Mysql') {
				$res = $this->db->fetchAll("SELECT SQL_CALC_FOUND_ROWS " . substr(trim($this->SQL), 6), $questions);
				$this->recordCount = $this->db->fetchOne("SELECT FOUND_ROWS()");
			} elseif ($this->config->database->adapter == 'pdo_pgsql') {
				$res = $this->db->fetchAll($this->SQL, $questions);
				$this->recordCount = $this->db->fetchOne("SELECT COUNT(1) FROM ({$this->SQL}) AS t", $questions);
			}

		}

		//echo round(microtime() - $a, 3);
		if (is_array($res) && $res) {
			$i = 0;
			foreach ($res[0] as $field => $sql_value) {
				$this->columnSchema[$field] = $i;
				$i++;
			}
			foreach ($res as $k => $row) {
				$this->data[$k] = array();
				$x = 0;
				foreach ($row as $sql_value) {
					if ($x == 0) {
						$this->data[$k][0] = $sql_value;
						$x++;
						continue;
					}
					if (isset($this->table_column[$this->main_table_id][$x - 1])) {
						$value = $this->table_column[$this->main_table_id][$x - 1];
					} else{
						$value = array();
						$value['type'] = '';
					}
					
					//$sql_value = stripslashes($sql_value);
					
					if ($value['type'] == 'function') {
						eval("\$sql_value = " . $value['processing'] . "(\$row);");
					} elseif ($value['type'] == 'html') {
						//
					} else {
						$sql_value = htmlspecialchars($sql_value);
					}
					$this->data[$k][] = $sql_value;
					$x++;
				}
			}
			if ($this->extOrder) {
				$orderType = isset($tmp['orderType']) ? $tmp['orderType'] : '';
			    $this->data = $this->array_key_multi_sort($this->data, $order, $orderType);
			}
		}
		$this->dataAlreadyGot = true;

		//SET META DATA
		foreach ($this->data as $k => $row) {
			if (!empty($this->paintCondition)) {
				if (!is_array($this->paintCondition)) {
					$this->paintCondition = array($this->paintCondition);
					$this->paintColor = array($this->paintColor);
					$this->fontColor = array($this->fontColor);
					$this->fontWeight = array($this->fontWeight);
				}
				foreach ($this->paintCondition as $ckey => $cvalue) {
					$tres = $this->replaceTCOL($row, $cvalue);
					$a = 0;
					eval("if ($tres) \$a = 1;");
					if ($a) {
						$this->metadata[$k] = array('paintColor' => '', 'fontColor' => '', 'fontWeight' => '');
						if (!empty($this->paintColor[$ckey])) $this->metadata[$k]['paintColor'] = $this->paintColor[$ckey];
						if (!empty($this->fontColor[$ckey])) $this->metadata[$k]['fontColor'] = $this->fontColor[$ckey];
						if (!empty($this->fontWeight[$ckey])) $this->metadata[$k]['fontWeight'] = $this->fontWeight[$ckey];
					}
				}
			}
		}
		return $this->data;
	}
	
	protected function array_key_multi_sort($arr, $l , $type) {
	    if ($type == 'asc') usort($arr, create_function('$a, $b', "return strnatcasecmp(\$a['$l'], \$b['$l']);"));
	    if ($type == 'desc') usort($arr, create_function('$a, $b', "return strnatcasecmp(\$b['$l'], \$a['$l']);"));
	    return($arr);
	}


	/**
	 * Default Delete function
	 * @throws Exception
	 * @return void
	 */
	private function deleteAction() {
		if (($this->checkAcl($this->resource, 'delete_all') || $this->checkAcl($this->resource, 'delete_owner')) && $this->deleteKey && !empty($_POST[$this->resource . "_delete"])) {
			$temp = explode(".", $this->deleteKey);
			$ids = $_POST[$this->resource . "_delete"];

			$authorOnly = false;
			if ($this->checkAcl($this->resource, 'delete_owner') && !$this->checkAcl($this->resource, 'delete_all')) {
				$authorOnly = true;
			}
			$this->db->beginTransaction();
			try {
				$is = $this->db->fetchAll("EXPLAIN " . $temp[0]);

				$nodelete = false;
				$noauthor = true;
				
				foreach ($is as $value) {
					if ($value['Field'] == 'is_deleted_sw') {
						$nodelete = true;
					}
					if ($authorOnly && $value['Field'] == 'author') {
						$noauthor = false;
					}
				}
				if ($authorOnly) {
					if ($noauthor) {
						throw new Exception("Данные не содержат признака автора!");
					} else {
						$auth 	= Zend_Registry::get('auth');
					}
				}
				if ($nodelete) {
					foreach ($ids as $key) {
						$where = array($this->db->quoteInto($temp[1] . " = ?", $key));
						if ($authorOnly) {
							$where[] = $this->db->quoteInto("author = ?", $auth->NAME);
						}
						$this->db->update($temp[0], array('is_deleted_sw' => 'Y'), $where);
					}
				} else {
					foreach ($ids as $key) {
						$where = array($this->db->quoteInto($temp[1] . " = ?", $key));
						if ($authorOnly) {
							$where[] = $this->db->quoteInto("author = ?", $auth->NAME);
						}
						$this->db->delete($temp[0], $where);
					}
				}
				$this->db->commit();
			} catch (Exception $e) {
				$this->db->rollback();
				$this->error = $e->getMessage();
			}
			unset($_POST[$this->main_table_id . "_delete"]);
		}
	}
	
	public function addHeader($cols) {
		$this->extraHeaders[] = $cols;
	}

	/**
	 * Create grid HTML
	 * @return void
	 */
	public function makeTable() {
		
		// CHECK FOR DELETE
		$this->deleteAction();
		if (!count($this->data) && !$this->dataAlreadyGot) {
			$this->data = $this->getData();
		} else {
			//$this->recordCount = count($this->data);
		}
		$ss = new Zend_Session_Namespace('Search');
		$ssi = $this->main_table_id;
		if (empty($ss->$ssi)) {
			$ss->$ssi = array();
		}
		$tmp = $ss->$ssi;

		$countPOST = 'count_' . $this->resource; //pagination record count
		$pagePOST = 'page_' . $this->resource; //pagination page number
		
		//TABLE HEAD
		$this->HTML .= "<div id=\"{$this->main_table_id}_error\" class=\"error" . ($this->error ? " block" : "") . "\">{$this->error}</div>";
		$this->HTML .= "<table width=\"100%\" id=\"list{$this->resource}\" class=\"sTable\">";
		$sqlSearchCount = 0;
		if (isset($this->table_search[$this->main_table_id]) && count($this->table_search[$this->main_table_id])) {

			//SEARCH BLOCK-----------------------------------------------------------------------------------------------
			if (!empty($tmp['search'])) {
				reset($_POST['search'][$this->main_table_id]);
				$next = current($_POST['search'][$this->main_table_id]);
			} else {
				$next = null;
			}

			$tpl = new Templater('core2/html/' . THEME . "/list/searchHead.tpl");
			$tpl->assign('{CLICK_FILTER}', "listx.showFilter('{$this->resource}')");
			$tpl->assign('{CLICK_START}', "listx.startSearch('{$this->resource}',$this->ajax);return false;");
			$tpl->assign('{filterID}', "filter" . $this->resource);
			if (count($tmp['search'])) {
				$tpl->touchBlock('clear');
				$tpl->assign('{CLICK_CLEAR}', "listx.clearFilter('{$this->resource}', $this->ajax)");
			}
			$tpl->touchBlock('fields');

			foreach ($this->table_search[$this->main_table_id] as $key => $value) {
				$searchFieldId = $this->search_table_id . $key;

				$tpl->assign('{FIELD_CAPTION}', $value['name']);

				$temp = explode("_", $value['type']);
				$value['type'] = $temp[0];

				$tpl2 = new Templater('core2/html/' . THEME . "/list/search_{$value['type']}.tpl");
				$tpl2->assign("{OUT}", $value['out']);
				$tpl2->assign("{NAME}", "search[$this->main_table_id][$key]");

				if ($value['type'] != 'checkbox') {
					$tpl2->assign("{ID}", $searchFieldId);
					$tpl2->assign("{ATTR}", $value['in']);
					$value['value'] = '';
					if (!empty($tmp['search'][$key])) {
						$value['value'] = $tmp['search'][$key];
					};
					$tpl2->assign("{VALUE}", $value['value']);
				}

				if ($value['type'] == 'text') {
					$tpl->assign('{FIELD_CONTROL}', $tpl2->parse());
				} elseif ($value['type'] == 'date') {
					$HTML = "<table><tr>";
					$dd = '';
					$mm = '';
					$yy = '';

					for ($d = 0; $d <= 1; $d++) {
						//$next[$d] = str_replace("--", "", $next[$d]);
						if ($next) {
							$dd = substr($next[$d], 8, 2);
							$mm = substr($next[$d], 5, 2);
							$yy = substr($next[$d], 0, 4);
						}
						$prefix = $searchFieldId . $d;
						$day	= '<input class="input" type="text" size="1" maxlength="2" autocomplete="OFF" id="' . $prefix . '_day" value="' . $dd . '" />';
						$month 	= '<input class="input" type="text" size="1" maxlength="2" autocomplete="OFF" id="' . $prefix . '_month" value="' . $mm . '" />';
						$year 	= '<input class="input" type="text" size="3" maxlength="4" autocomplete="OFF" id="' . $prefix . '_year" value="' . $yy . '" />';
						$insert = str_replace("dd", $day, strtolower($this->date_mask));
						$insert = str_replace("mm", $month, $insert);
						$insert = str_replace("yyyy", $year, $insert);
						$insert = str_replace("yy", $year, $insert);
						$HTML .= 								"<td class=\"searchDateTd\">".
																		"<input id=\"date_" . $prefix . "\" type=\"hidden\" name=\"search[{$this->main_table_id}][$key][]\" value=\"{$next[$d]}\">" . $insert .
																	"</td>";
						$insert = "<img class=\"searchDateImg\" src=\"core2/html/" . THEME . "/img/calendar.png\" onclick=\"listx.create_date('$prefix')\">";
						$insert .= "<div id=\"cal$prefix\" class=\"calSearchContent\"></div>";

						$HTML .= 								"<td class='searchDateImgTD'>".$insert."</td>";
						if ($d == 0) $HTML .= 				"<td style='padding:0'>&nbsp;&nbsp;<>&nbsp;&nbsp;</td>";
					}
					$HTML .= "<td style=\"searchDateMask\">&nbsp;&nbsp;".$this->date_mask."</td>".
									"<td style=\"padding:0\">".$value['out']."</td>".
								"</tr>".
							"</table>";
					$tpl->assign('{FIELD_CONTROL}', $HTML);
				} elseif ($value['type'] == 'checkbox') {
					$temp = array();
					if (is_array($this->sqlSearch[$sqlSearchCount])) {
						foreach ($this->sqlSearch[$sqlSearchCount] as $k => $v) {
							$temp[] = array($k, $v);
						}
					} else {
						$temp = $this->db->query($this->sqlSearch[$sqlSearchCount]);
					}
					$tpl2->touchBlock('checkbox');
					foreach ($temp as $row) {
						$k = current($row);
						$v = end($row);

						$tpl2->assign("{ID}", $searchFieldId . "_" . $k);
						$tpl2->assign("{VALUE}", $k);
						$tpl2->assign("{LABEL}", $v);
						if (in_array($row[0], $next)) {
							$tpl2->assign("{checked}", "checked=\"checked\"");
						} else {
							$tpl2->assign("{checked}", "");
						}
						$tpl2->reassignBlock('checkbox');
					}
					$sqlSearchCount++;
					$tpl->assign('{FIELD_CONTROL}', $tpl2->parse());
				} elseif ($value['type'] == 'radio') {
					$temp = array();
					if (is_array($this->sqlSearch[$sqlSearchCount])) {
						foreach ($this->sqlSearch[$sqlSearchCount] as $k => $v) {
							$temp[] = array($k, $v);
						}
					} else {
						$temp = $this->db->query($this->sqlSearch[$sqlSearchCount]);
					}
					$tpl2->touchBlock('radio');
					foreach ($temp as $row) {
						$k = current($row);
						$v = end($row);
						if (is_array($v) && isset($v['value'])) {
							$v = $v['value'];
						}
						$tpl2->assign("{IDK}", $searchFieldId . "_" . $k);
						$tpl2->assign("{VALUE}", $k);
						$tpl2->assign("{LABEL}", $v);
						if ($row[0] === $next) {
							$tpl2->touchBlock('checked');
						}
						$tpl2->reassignBlock('radio');
					}
					$sqlSearchCount++;
					$tpl->assign('{FIELD_CONTROL}', $tpl2->parse());
				} elseif ($value['type'] == 'list') {
					$temp = array();
					if (is_array($this->sqlSearch[$sqlSearchCount])) {
						foreach ($this->sqlSearch[$sqlSearchCount] as $k => $v) {
							$temp[] = array($k, $v);
						}
					} else {
						$temp = $this->db->fetchAll($this->sqlSearch[$sqlSearchCount]);
					}
					$opt = array('' => 'Все');
					foreach ($temp as $row) {
						$k = current($row);
						$v = end($row);
						if (is_array($v) && isset($v['value'])) {
							$v = $v['value'];
						}
						$opt[$k] = $v;
					}
					$sqlSearchCount++;
					$tpl2->fillDropDown('{ID}', $opt, $next);
					$tpl->assign('{FIELD_CONTROL}', $tpl2->parse());
				}

				if (isset($_POST['search'][$this->main_table_id]) && $_POST['search'][$this->main_table_id]) {
					$next = next($_POST['search'][$this->main_table_id]);
				}
				
				$tpl->reassignBlock('fields');
			}
			$this->HTML .= 	$tpl->parse();
		}
		
		//SERVICE ROW
		$tpl = new Templater('core2/html/' . THEME . "/list/serviceHead.tpl");
		$tpl->assign('[TOTAL_RECORD_COUNT]', ($this->roundRecordCount ? "~" : "") . '[TOTAL_RECORD_COUNT]');
		$buttons = '';
		if (!empty($this->table_button[$this->main_table_id])) {
			reset($this->table_button[$this->main_table_id]);
			foreach ($this->table_button[$this->main_table_id] as $button_key => $button_value) {
				if (empty($button_value['html'])) {
					$buttons .= $this->button($button_value['name'], "", "listx.buttonAction('{$this->resource}', '{$button_value['url']}', '{$button_value['confirm']}', {$button_value['nocheck']}, this)");
				} else {
					$buttons .= $button_value['html'];
				}
			}
		}
		$tpl->assign('[BUTTONS]', $buttons);
		if ($this->checkAcl($this->resource, 'edit_all') || $this->checkAcl($this->resource, 'edit_owner') && ($this->checkAcl($this->resource, 'read_all') || $this->checkAcl($this->resource, 'read_owner'))) {
			//if ($this->multiEdit) $serviceHeadHTML .= 	$this->button($this->classText['EDIT'], "", "multiEdit('$this->editURL', '$this->main_table_id')");
			if ($this->addURL) {
				$tpl->touchBlock('add_button');
				$tpl->assign('Добавить', $this->classText['ADD']);
				if (substr($this->addURL, 0, 11) == 'javascript:') {
					$tpl->assign('[addURL]', substr($this->addURL, 11));
				} else {
					$tpl->assign('[addURL]', "load('{$this->addURL}')");
				}
			}
		}
		if (($this->deleteURL || $this->deleteKey) && ($this->checkAcl($this->resource, 'delete_all') || $this->checkAcl($this->resource, 'delete_owner'))) {
			$tpl->touchBlock('del_button');
			if ($this->deleteURL) {
				$tpl->assign('[delURL]', $this->deleteURL);
			} else {
				$tpl->assign('[delURL]', "listx.del('{$this->resource}', '{$this->classText['DELETE_MSG']}', $this->ajax)");
			}
		}
		$serviceHeadHTML = $tpl->parse();
		
		// DATA HEADER
		$tpl = new Templater("core2/html/" . THEME . "/list/headerHead.tpl");
		$tpl->assign('{main_table_id}', $this->main_table_id);
		$tpl->assign('{resource}', $this->resource);
		$tpl->assign('isAjax', $this->ajax);
		$eh = count($this->extraHeaders);
		if ($eh) {
			$tpl->assign('{ROWSPAN}', $eh + 1);
		} else {
			$tpl->assign('{ROWSPAN}', 1);
		}
		$temp = '';
		$columnsToReplace = array();
		if ($eh) {
			$cell = $tpl->getBlock('extracell');
			$tpl->assign('{ROWSPAN}', $eh + 1);
			foreach ($this->extraHeaders as $k => $cols) {
				foreach ($cols as $caption => $span) {
					if (isset($span['replace']) && $span['replace']) {
						$columnsToReplace[] = $k;
					}
					if (!isset($span['col'])) $span['col'] = 1;
					if (!isset($span['row'])) $span['row'] = 1;
					$temp .= str_replace(array('{CAPTION}', '{COLSPAN}', '{ROWSPAN2}'), 
								array($caption, $span['col'], $span['row']), 
								$cell);
				}
			}
			$tpl->replaceBlock('extracell', $temp);
			$tpl->touchBlock('extrahead');
		} else {
			$tpl->assign('{ROWSPAN}', 1);
		}
		$temp = '';
		$cell = $tpl->getBlock('cell');
		$cellnosort = $tpl->getBlock('cellnosort');
		foreach ($this->table_column[$this->main_table_id] as $key => $value) {
			if (in_array($key, $columnsToReplace)) continue;
			if ($value['sort']) {
				$img = '';
				if (!empty($tmp['order'])) {
					if ($tmp['order'] == $key + 1) {
						if ($tmp['orderType'] == "asc") {
							$img = "core2/html/".THEME."/img/asc.gif";
						}
						if ($tmp['orderType'] == "desc") {
							$img = "core2/html/".THEME."/img/desc.gif";
						}
						$img = '<img src="' . $img . '" alt=""/>';
					}
				}
				$temp .= str_replace(array('{WIDTH}', '{ORDER_VALUE}', '{CAPTION}', '{ORDER_TYPE}', '{COLSPAN}'), 
									array($value['width'], ($key + 1), $value['name'], $img, ''), 
									$cell);
			} else {
				$temp .= str_replace(array('{WIDTH}', '{CAPTION}', '{COLSPAN}'), 
									array($value['width'], $value['name'], ''), 
									$cellnosort);
			}
		}
		$tpl->replaceBlock('cell', $temp);
		if ($this->noCheckboxes == 'no') {
			$tpl->touchBlock('checkboxes');
		}
		$headerHeadHTML = $tpl->parse();
		//TABLE BODY.
		$tableBodyHTML = '';
		$int_count = 0;
		if (!$this->extOrder) {
			$recordNumber = ($_POST[$pagePOST] - 1) * $_POST[$countPOST];
		}
		
		if (count($this->addSum)) {
			$needsum = array();
		} else {
			$needsum = 0;
		}
		
		//BUILD ROWS WITH DATA
		//echo "<PRE>";print_r($this->data);echo"</PRE>";die();
		$i = 0;
		if (!empty($this->customSearch)) { //CHECK SEACH FOR CUSTOM FIELDS
			$newData = array();
			foreach ($this->data as $row) {
			
				$skip = false;
				foreach ($this->customSearch as $field => $search_value) {
					$search_value_trimmed = trim($search_value, '%');
					if (!empty($search_value) && $search_value_trimmed && isset($this->columnSchema[$field])) {
						if (substr($search_value, 0, 1) != '%' && substr($search_value, -1) != '%') {
							if ($row[$this->columnSchema[$field]] != $search_value_trimmed) $skip = true;
						} elseif (substr($search_value, 0, 1) == '%' && substr($search_value, -1) != '%') {
							if (strripos($row[$this->columnSchema[$field]], $search_value_trimmed) !== 0) $skip = true;
						} else {
							if (stripos($row[$this->columnSchema[$field]], $search_value_trimmed) === false) $skip = true;
						}
					}
				}
				if ($skip) {
					$this->recordCount--;
				} else {
					$newData[] = $row;
				}
			}
			$this->data = $newData;
		}

		foreach ($this->data as $k => $row) {
			
			if ($this->extOrder) {
				if ($i < $_POST[$pagePOST] * $_POST[$countPOST] - $_POST[$countPOST]) {
					$i++;
					continue;
				}
				if ($i + 1 > $_POST[$pagePOST] * $_POST[$countPOST]) break;
				$recordNumber = $i + 1;
			} else {
				$recordNumber++;
			}
			
			$recordClass = ""; //TODO add class
			
			$tableBodyHTML .= "<tr onmouseover=\"this.className='rowOver'\" onmouseout=\"this.className=''\"";
			if (!empty($this->metadata[$k])) {
				if ($this->metadata[$k]['paintColor']) {
					$tableBodyHTML .= " bgcolor=\"{$this->metadata[$k]['paintColor']}\"";
				}
				if ($this->metadata[$k]['fontColor'] || $this->metadata[$k]['fontWeight']) {
					$tableBodyHTML .= " style=\"";
					if ($this->metadata[$k]['fontColor']) $tableBodyHTML .= "color:{$this->metadata[$k]['fontColor']};";
					if ($this->metadata[$k]['fontWeight']) $tableBodyHTML .= "font-weight:{$this->metadata[$k]['fontWeight']};";
					$tableBodyHTML .= "\"";
				}
			}

			if ($this->editURL && 
				($this->checkAcl($this->resource, 'edit_all') || $this->checkAcl($this->resource, 'edit_owner')
				|| $this->checkAcl($this->resource, 'read_all') || $this->checkAcl($this->resource, 'read_owner'))) {
				
				$tres = $this->replaceTCOL($row, $this->editURL);
				$tableBodyHTML .= ' style="cursor:pointer"';
				if (strpos(strtolower($tres), 'javascript:') === 0) {
					$tableBodyHTML .= ' onclick="' . substr($tres, 11) . '"';
				} else {
					$tableBodyHTML .= ' onclick="load(\'' . $tres . '\')"';
				}
			}
			$tableBodyHTML .= " class=\"editListFields\"><td title=\"{$row[0]}\">$recordNumber</td>";
			$c = 1;
			$look = "";
			
			$columnCount = count($this->table_column[$this->main_table_id]);
			
			reset($row);
			next($row);
			for ($sql_key = 1; $sql_key <= $columnCount; $sql_key++) {
				//$sql_value = $row[$sql_key];
				$sql_value = current($row);
				next($row);
				if (is_array($needsum)) {
					$need = 'TCOL_' . substr("0" . $sql_key, -2);
					if (in_array($need, $this->addSum)) {
						$needsum[$need] += $sql_value;
					}
				}
				
				$value = $this->table_column[$this->main_table_id][$sql_key - 1];
				$temp = "";
				if ($value['type'] == 'block') {
					$temp = " onclick=\"listx.cancel(event)\"";
				}
				
				$tableBodyHTML .= "<td width=\"{$value['width']}\"" . $temp;
				if ($value['type'] != 'status_inline') {
					$tableBodyHTML .= " " . $this->replaceTCOL($row, $value['in']); 
				}
				$tableBodyHTML .= ">";
				
				//RECOGNIZE TYPE 
				//$sql_value = htmlspecialchars($sql_value); 
				
				if ($value['type'] == 'text' || $value['type'] == 'block' || $value['type'] == 'function') {
					$tableBodyHTML .= $sql_value;
				} elseif ($value['type'] == 'number') {
					$tableBodyHTML .= $this->commafy($sql_value);
				} elseif ($value['type'] == 'file') {
					global $config;
					if (!class_exists('FileMaster')) {
						require_once('FileMaster.php');
					}						
					$tableBodyHTML .= FileMaster::getFileInfoForList($sql_value, '', 150, 150);//htmlspecialchars_decode($sql_value);
					
				} elseif ($value['type'] == 'html') {
					$tableBodyHTML .= htmlspecialchars_decode($sql_value);
				} else if ($value['type'] == 'date') {
					$dd = substr($sql_value, 8, 2);
					$mm = substr($sql_value, 5, 2);
					$yyyy = substr($sql_value, 0, 4);
					$yy = substr($sql_value, 2, 2);
					
					$tableBodyHTML .= str_replace(array("dd", "mm", "yyyy", "yy"), array($dd, $mm, $yyyy, $yy), strtolower($this->date_mask));
					
				} else if ($value['type'] == 'datetime') {
					$dd = substr($sql_value, 8, 2);
					$mm = substr($sql_value, 5, 2);
					$yyyy = substr($sql_value, 0, 4);
					$yy = substr($sql_value, 2, 2);
					$time = substr($sql_value, 11);
					
					$sql_value = str_replace(array("dd", "mm", "yyyy", "yy"), array($dd, $mm, $yyyy, $yy), strtolower($this->date_mask));
					$tableBodyHTML .= $sql_value . ' ' . $time;
				} else if ($value['type'] == 'datetime_human') {
					require_once('humanRelativeDate.class.php');
					$humanRelativeDate = new HumanRelativeDate();
					$dd = substr($sql_value, 8, 2);
					$mm = substr($sql_value, 5, 2);
					$yyyy = substr($sql_value, 0, 4);
					$yy = substr($sql_value, 2, 2);
					$time = substr($sql_value, 11);

					$title = str_replace(array("dd", "mm", "yyyy", "yy"), array($dd, $mm, $yyyy, $yy), strtolower($this->date_mask)) . ' ' . $time;
					$tableBodyHTML .= "<span title=\"$title\">{$humanRelativeDate->getTextForSQLDate($sql_value)}</span>";
				} else if ($value['type'] == 'look') {
					$tableBodyHTML .= "<div onclick='listx.cancel2(event, \"look" . $this->main_table_id . $int_count . "\");'>" . stripslashes($sql_value) . "</div>";
					$look = $this->replaceTCOL($row, $value['processing']);
				} else if ($value['type'] == 'hint') {
					$SQL = $this->replaceTCOL($row, $value['processing']);
					$hint_res = $this->db->fetchAll($SQL);
					$hint = "<table class=\"editHintTable\">";
					foreach ($hint_res as $hint_row) {
						$hint .= "<tr>";
						foreach ($hint_row as $hint_key => $hint_value) {
							$hint .= "<td>$hint_value</td>";
						}
						$hint .= "</tr>";
					}
					$hint .= "</table>";
					$tableBodyHTML .= "<span class=\"editHintSpan\" onmouseover=\"this.nextSibling.style.display='block'\" onmouseout=\"this.nextSibling.style.display='none'\">$sql_value</span><div class=\"editHintDiv\" style=\"display:none;\">".$hint."</div>";
				} elseif ($value['type'] == 'status') {
					if ($sql_value == 1 || $sql_value == 'Y' || $sql_value == '[ON]') {
						$tableBodyHTML .= "<img src=\"core2/html/" . THEME . "/img/on.png\" alt=\"on\" />";
					} else {
						$tableBodyHTML .= "<img src=\"core2/html/" . THEME . "/img/off.png\" alt=\"off\" />";
					}
				} elseif ($value['type'] == 'status_inline') {
					$evt = "";
					if ($this->checkAcl($this->resource, 'edit_owner')) {
						$evt = "onclick=\"listx.switch_active(this, event)\" t_name=\"{$value['in']}\" val=\"{$row[0]}\"";
					}
					if ($sql_value == 1 || $sql_value == 'Y' || $sql_value == '[ON]') {
						$tableBodyHTML .= "<img src=\"core2/html/" . THEME . "/img/on.png\" alt=\"on\" $evt />";
					} else {
						$tableBodyHTML .= "<img src=\"core2/html/" . THEME . "/img/off.png\" alt=\"off\" $evt />";
					}
				}
				
				$tableBodyHTML .= "</td>";
			}
			if ($this->multiEdit) {
				$onclick = "onclick=\"listx.cancel(event, '{$this->main_table_id}')\"";
			} else {
				$onclick = "onclick=\"listx.cancel(event)\"";
			}
			$tempid = $this->resource . $int_count;
			if ($this->noCheckboxes == 'no') {
				$tableBodyHTML .= "<td width=\"1%\"><input class=\"checkbox\" type=\"checkbox\" id=\"check{$tempid}\" name=\"check{$tempid}\" value=\"{$row[0]}\" $onclick></td>";	
			}
			$tableBodyHTML .= "</tr>";
			if (isset($look) && $look) {
				$tableBodyHTML .= "<tr id=\"look{$tempid}\" style=\"display:none\"><td colspan=\"100\">$look</td></tr>";
			}
			$int_count++;
			$i++;
		}

		if (!$this->recordCount || $this->recordCount < 0) {
			$tableBodyHTML = "<tr><td colspan=\"100\" align=\"center\" style=\"padding:5\">{$this->classText['NORESULT']}</td></tr>";
		} else {
			// SUMM ROW
			if (isset($this->addSum) && $count = count($this->addSum)) {
				$is = implode(",", $this->addSum);
				$tableBodyHTML .= "<tr class=\"headerText\">";
				$j = 1;
				$gotit = 0;
				for ($i = 0; $i < $j; $i++) {
					$need = 'TCOL_' . substr("0" . $i, -2);
					if (strpos($is, $need) !== false) {
						$gotit++;
						$tableBodyHTML .= "<td align=\"right\" nowrap=\"nowrap\">".$this->commafy($needsum[$need])."</td>";
					} else {
						$tableBodyHTML .= "<td></td>";
					}
					if ($gotit < $count) {
						$j++;
					}
				}
				$tableBodyHTML .= "<td colspan=100></td></tr>";
			}
		}
		
		$this->HTML .= str_replace('[TOTAL_RECORD_COUNT]', $this->recordCount, $serviceHeadHTML) . $headerHeadHTML . $tableBodyHTML;
		
		// FOOTER ROW
		$tpl = new Templater("core2/html/" . THEME . "/list/footerx_controls.tpl");
		$count = ceil($this->recordCount / $this->recordsPerPage);

		//PAGINATION
		$pages = ceil($this->recordCount / $_POST[$countPOST]);
		$tpl->assign('{CURR_PAGE}', $_POST[$pagePOST] . " " . $this->classText['FROM'] . " " . $pages);
		$tpl->assign('[IDD]', 'pagin_' . $this->resource);
		$tpl->assign('[ID]', $this->resource);
		if ($count > 1) {
			$tpl->touchBlock('pages');
			$tpl->assign('{GO_TO_PAGE}', "listx.goToPage(this, '$this->resource', $this->ajax)");

			if ($_POST[$pagePOST] > 1) {
				$tpl->touchBlock('pages2');
				$tpl->assign('{BACK}', $_POST[$pagePOST] - 1);
			}
			if ($_POST[$pagePOST] < $count) {
				$tpl->touchBlock('pages3');
				$tpl->assign('{FORW}', $_POST[$pagePOST] + 1);
			}
			$tpl->assign('{GO_TO}', "listx.pageSw(this, '$this->resource', $this->ajax)");
			$tpl->touchBlock('recordsPerPage');
			$tpl->assign('{SWITCH_CO}', "listx.countSw(this, '$this->resource', $this->ajax)");
			$opts = array();
			$notoall = false;
			for ($k = 0; $k < $count - 1; $k++) {
				$val = $this->recordsPerPage * ($k + 1);
				if ($val > 1000) {
					$notoall = true;
					break;
				}
				$opts[$val] = $val;
			}
			if (!$notoall) {
				$opts[1000] = $this->classText['PAGIN_ALL'];
			}
			$tpl->fillDropDown("footerSelectCount", $opts, $_POST[$countPOST]);
			$tpl->assign('footerSelectCount', $this->main_table_id . 'footerSelectCount');
		}
		$this->HTML .= 	$tpl->parse() . '</table>';

		return $this->HTML;
	}

	/**
	 * Print grid HTML
	 * @return void
	 */
	public function showTable() {
		if ($this->checkAcl($this->resource, 'list_all') || $this->checkAcl($this->resource, 'list_owner')) {
			$this->makeTable();
			echo "<script>if (!listx){alert('listx не найден!')}
				else {
					listx.loc['{$this->resource}'] = '{$_SERVER['QUERY_STRING']}';
				}
			</script>";
			echo $this->HTML;

			//добавление скрипта для сортировки
			if ($this->table && $this->is_seq) {
				echo '<script>
							$(function() {
								listx.initSort("' . $this->resource . '", "' . $this->table . '");
							});
							</script>
						';
			}
		}
	}

	/**
	 * Allow to replace TCOL_ or TCOL64_ in any string
	 * Example: TCOL_01 will be replaced by $row[1]
	 * @param Array $row - data for replace
	 * @param string $tcol - expression where to find TCOL_ construction
	 * @return string
	 */
	private function replaceTCOL($row, $tcol) {
		$tres = "";
		$temp = explode("TCOL_", $tcol);
		foreach ($temp as $tkey => $tvalue) {
			$index = substr($tvalue, 0, 2) * 1;
			if ($tkey == 0) {
				$tres .= $tvalue;
			} elseif (isset($row[$index])) {
				if (strpos($row[$index], "'") !== false) {
					$row[$index] = addslashes($row[$index]);
				}
				$tres .= $row[$index] . substr($tvalue, 2);
			} else {
				$tres .= substr($tvalue, 2);
			}
		}
		$temp = explode("TCOL64_", $tres);
		$tres2 = "";
		foreach ($temp as $tkey => $tvalue) {
			$index = substr($tvalue, 0, 2) * 1;
			if ($tkey == 0) {
				$tres2 .= $tvalue;
			} elseif (isset($row[$index])) {
				$tres2 .= base64_encode(htmlspecialchars_decode($row[$index])) . substr($tvalue, 2);
			} else {
				$tres2 .= substr($tvalue, 2);
			}
		}
		if (!$tres2) $tres2 = $tres;
		return $tres2;
	}
	
	function addParams($va, $value) {
		$this->params[$va] = $value;
	}

}
