<?php
/**
 * ����� ����������� �� ������ ��������� ������ � ������� ����� ������� ��� ������.
 */
class searcher
{
  /**
   * ��������� ������� ��� ������ �� ���������� �������.
   *
   * @param string $search ��������� ������
   * @param array $fields ������ �����, �� ������� �������������� �����
   * @return string ������� WHERE ��� ������
   */
  function get_search_condition($search, $fields)
  {
    $terms = $this->build_search_terms($search);
    return $this->build_terms_clause($terms, $fields);
  }

  /**
   * ��������� ������ ��������� ����.
   *
   * @param string $search ���������� ������.
   * @return array ������ ��������� ����.
   */
  function build_search_terms($search)
  {
  	$terms = array();
  	$match = array();
  	if (strstr($search, '"'))
  	{
  		if (strstr($search, "\""))
  		{
  			$search_string = $search;
  			while (ereg('-*"[^"]*"', $search_string, $match))
  			{
  				$terms[] = trim(str_replace("\"", "", $match[0]));
  				$search_string = substr(strstr($search_string, $match[0]), strlen($match[0]));
  			}
  		}
  	}
  	$search = ereg_replace('-*"[^"]*"', '', $search);
  	$regular_terms = explode(" ", $search);
  	while (list($key, $val) = each($regular_terms))
  	{
  		if ($val != "") $terms[] = trim($val);
  	}
  	return $terms;
  }

  /**
   * ��������� ������� ��� ������ �� ������� ��������� ����.
   *
   * @param array $terms ����� ��������� ����
   * @param array $fields ������ �����, �� ������� �������������� �����.
   * @return string
   */
  function build_terms_clause($terms, $fields)
  {
    $where_clause = '';
		while (list($junk, $term) = each($terms))
		{
			$cmpfunc = "ILIKE";
  		$cmptype = "OR";
			if (substr($term, 0, 1) == "-")
			{
				$term = substr($term, 1);
				$cmpfunc = "NOT ILIKE";
    		$cmptype = "AND";
			}
			reset($fields);
			unset($likeArray);
			while (list($key, $val) = each($fields))
			{
				$likeArray[] = " $val $cmpfunc '%$term%' ";
			}
			$termArray[] = " (" . implode ( $likeArray, " $cmptype " ) . ") ";
		}
		$cmptype = "AND";
		$where_clause .= " (" . implode ( $termArray, " $cmptype " ) . ") ";
  	return $where_clause;
  }
}
?>