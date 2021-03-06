<?
/**
 * ���������� ���� � ��������� ���������
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/stdf.php");

/**
 * ���������� ��������� ����� (���������).
 */
class portfolio_filters
{
	
	/**
	 * ���������� ������� ������������.
	 *
	 * @param integer $user_id ��� �������������
	 * @param real $cost_from ����������� ���������
	 * @param real $cost_to ������������ ���������
	 * @param integer $cost_type ��� ������
	 * @param string $rerror
	 * @param string $error
	 * @param integer $force
	 */
	function Save($user_id, $cost_from, $cost_to, $cost_type, &$rerror, &$error, $force=0) {
   		if ($this->IsFilter($user_id)) {
   		    $error = $this->Update($user_id, $cost_from, $cost_to, $cost_type);
   		} else {
   		  	$error = $this->Add($user_id, $cost_from, $cost_to, $cost_type);
   		}
	}

	/**
	 * ���������� ������� ����������� ������������.
	 *
	 * @todo ������� $force, �� ������������ � �������
	 * 
	 * @global array $session ������ ������������
	 * 
	 * @param integer $user_id ��� �������������
	 * @param real $cost_from ����������� ���������
	 * @param real $cost_to ������������ ���������
	 * @param integer $cost_type ��� ������
	 * @param integer $force  �� ����������� � ������� 
	 * @return string ��������� �� ������ ��� ������ ������
	 */
	function Add($user_id, $cost_from, $cost_to, $cost_type, $force=0) {
		global $session;
	    $cost_from = intval(str_replace(" ", "", $cost_from) * 100) / 100;
	    $cost_to = intval(str_replace(" ", "", $cost_to) * 100) / 100;
	    $cost_type = intval($cost_type);

		if ($user_id > 0) {
		    global $DB;
		    $data = compact( 'user_id', 'cost_from', 'cost_to', 'cost_type' );
		    $data['is_active'] = 't';
	  		$DB->insert( 'portfolio_filters', $data );
	  		$error = $DB->error;
		} else {
			$_SESSION['pf'] = array (
				       'user_id' => 0, 
				       'cost_from' => $cost_from,
				       'cost_to' => $cost_to,
				       'cost_type' => $cost_type,
				       'is_active' => 't'
				      );
		  	$error = '';
		}
		return $error;
	}

	/**
	 * ���������� ������������� ������� ����������� �����������.
	 *
	 * @todo ������� $force, �� ������������ � �������
	 * 
	 * @global array $session ������ ������������
	 * 
	 * @param integer $user_id ��� �������������
	 * @param real $cost_from ����������� ���������
	 * @param real $cost_to ������������ ���������
	 * @param integer $cost_type ��� ������
	 * @param integer $force
	 * @return string ��������� �� ������ ��� ������ ������
	 */
	function Update($user_id, $cost_from, $cost_to, $cost_type, $force=0) {
	    $cost_from = intval(str_replace(" ", "", $cost_from) * 100) / 100;
	    $cost_to = intval(str_replace(" ", "", $cost_to) * 100) / 100;
	    $cost_type = intval($cost_type);
        
		if ($user_id > 0) {
		    global $DB;
		    $data = compact( 'cost_from', 'cost_to', 'cost_type' );
		    $data['is_active'] = 't';
	  		$DB->update( 'portfolio_filters', $data, 'user_id = ?i', $user_id );
	  		$error = $DB->error;
		} else {
			$_SESSION['pf'] = array(
				       'user_id' => 0, 
				       'cost_from' => $cost_from,
				       'cost_to' => $cost_to,
				       'cost_type' => $cost_type,
				       'is_active' => 't'
				      );
		  	$error = '';
		}
		return $error;
	}
	
	/**
	 * ���������� ������
	 *
	 * @param integer $user_id �� ������������
	 */
	function setActive($user_id) {
	   global $session; 
	   if ($user_id > 0) {
	  		global $DB;
	  		$DB->update( 'portfolio_filters', array('is_active' => 't'), 'user_id=?i', $user_id );
	  		$error = $DB->error;
	   } else {
            $_SESSION['pf']['is_active']='t';
	   }
	}

  /**
   * ��������� ������� ����������� �����.
   *
   * @global array $session ������ ������������
   * 
   * @param integer $user_id ��� �����
   * @param string $error ���������� ��������� �� ������ ��� ������ ������
   * @return array ������
   */
	function GetFilter($user_id, &$error) {
		if ($user_id > 0) {
		    global $DB;
	  		$sql    = "SELECT * FROM portfolio_filters WHERE user_id=?";
	  		$ret    = $DB->row( $sql, $user_id );
	  		$error .= $DB->error;
	  		
	  		if (isset($ret['cost_from'])) {
	  			$ret['cost_from'] = intval(str_replace(" ", "", $ret['cost_from']) * 100) / 100;
	  		  	if ($ret['cost_from'] == 0) $ret['cost_from'] = '';
	  		}
	  		if (isset($ret['cost_to'])) {
	  			$ret['cost_to'] = intval(str_replace(" ", "", $ret['cost_to']) * 100) / 100;
	  		  	if ($ret['cost_to'] == 0) $ret['cost_to'] = ''; 
	  		}
	  		if (isset($ret['cost_type'])) {
	  		  	$ret['cost_type'] = floatval($ret['cost_type']);
	  		  	if ($ret['cost_type'] == 0) $ret['cost_type'] = '';
	  		}
	  		if (!$ret) {
		        $ret = array (
		         'user_id' => $user_id, 
		         'cost_from' => '',
		         'cost_to' => '',
		         'cost_type' => ''
		        );
	      	}
		} else {
			$ret = $_SESSION['pf'];
 			if (!$ret) $ret = array('user_id' => 0,  'cost_from' => '', 'cost_to' => '', 'cost_type' => '');
		}

		return $ret;
	}
	
  /**
   * �������� ������� ����������� �����
   *
   * @global array $session ������ ������������
   * 
   * @param integer $user_id �� ������������
   * @return string ��������� �� ������ ��� ������ ������
   */
	function DeleteFilter($user_id)
	{
	  global $session;
		if ($user_id > 0) {
      		//$sql = "DELETE FROM portfolio_filters WHERE (user_id='$user_id')";
      		global $DB;
      		$DB->update('portfolio_filters', array('is_active'=>'f'), 'user_id=?i', $user_id);
      		$error .= $DB->error;
		}
		else
		{
		  $_SESSION['pf']['is_active'] = 'f';
		  $error = '';
		}
		return $error;
	}

  /**
   * �������� ������������� ������� ����������� �����
   *
   * @global array $session ������ ������������
   * 
   * @param integer $user_id �� ������������
   * @return boolean true, ���� ������ ����������, false, ���� ���
   */
	function IsFilter($user_id)
	{
	  global $session;
		if ($user_id > 0) {
		    global $DB;
            $num = $DB->val( "SELECT count(*) FROM portfolio_filters WHERE user_id=?", $user_id );
            $ret = !(!$num || $num == 0);
		} else {
            $ret = (!!$_SESSION['pf']);
		}
		return $ret;
	}

	/**
	 * ������������ ���������� ��������� � �������.
	 *
	 * @param array $filter ������
	 * @param boolean $filter_apply �������� ������ ��� ���
	 * @return string ��������� ��������� � �������
	 */
	function GetDescription($filter, $filter_apply)
	{
	  $ret = '';
	  if ($filter_apply)
	  {
	    if ((isset($filter['cost_from']) && !empty($filter['cost_from'])) || (isset($filter['cost_to']) && !empty($filter['cost_to'])))
	    {
 	      $ret .= (($ret =='') ? '' : ' ') . '��������� ������ ';

	      $ret .= view_range_cost2($filter['cost_from'], $filter['cost_to'], '', '', false, $filter['cost_type']);
/*
  	    if (isset($filter['cost_from']) && !empty($filter['cost_from']))
  	    {
  	      $ret .= (($ret =='') ? '' : ' ') . '�� $' . $filter['cost_from'];
  	    }
  	    if (isset($filter['cost_to']) && !empty($filter['cost_to']))
  	    {
  	      $ret .= (($ret =='') ? '' : ' ') . '�� $' . $filter['cost_to'];
  	    }
*/
	    }
	  }
	  else
	  {
	    $ret = '��������';
	  }
	  return $ret;
	}
}
?>