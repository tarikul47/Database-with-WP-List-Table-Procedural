<?php
if ( ! class_exists( "WP_List_Table" ) ) {
	require_once( ABSPATH . "wp-admin/includes/class-wp-list-table.php" );
}

class DBTableUsers extends WP_List_Table{

    private $_items;
    function __construct($data)
    {
        parent::__construct();
        $this->_items =$data;
    }

    function get_columns()
    {
        return [
            'cb' => '<input type="checkbox">',
            'name'=>__('Name','database'),
            'email'=>__('Email','databse'),
            'action'=>__('Action','databse'),
        ];
    }

    function column_name($item)
    {
        $nonce = wp_create_nonce('database_edit');
        $actions = [
            'edit' => sprintf('<a href="?page=databse&pid=%s&n=%s">%s</a>',$item['id'],$nonce, __("Edit",'database')),
            'delete' => sprintf('<a href="?page=databse&pid=%s&n=%s&action=%s">%s</a>',$item['id'],$nonce,'delete', __("Delete",'database')),
        ];

        return sprintf('%s %s',$item['name'],$this->row_actions($actions));
    }

    function column_cb($item)
    {
        return "<input type='checkbox' value='{$item['id']}'>";
    }
    function column_action($item)
    {
        $link = wp_nonce_url( admin_url('?page=databse&pid=').$item['id'],"database_edit", 'n' );
        return "<a href='".esc_url($link)."'>Edit</a>";
    }


    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $per_page = 3;
        $current_page = $this->get_pagenum();
        $total_items = count($this->_items);
        $this->set_pagination_args(
            [
                'total_items' => $total_items,
                'per_page' =>$per_page,
            ]

        );
        $this->_column_headers = array($columns, $hidden, $sortable);
        /**
         * $current page =  1,
         * 
         */
        $data = array_slice($this->_items,($current_page-1)*$per_page, $per_page);
        $this->items = $data;
    }

    function column_default($item, $column_name)
    {
        switch( $column_name ) { 
            case 'name':
            case 'email':
              return $item[ $column_name ];
            default:
              return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
          }
    }
}