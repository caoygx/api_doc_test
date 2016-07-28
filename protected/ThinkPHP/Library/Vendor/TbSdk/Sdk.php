<?php
header("Content-type: text/html; charset=utf-8");
    class Sdk {
        public function search($title,$page_no,$page_size){
            include "TopSdk.php";
            $c = new TopClient;
            $c->appkey = '23047477';
            $c->secretKey = '075295c35ac3e2ba59097487a089b444';
            $req = new ProductsSearchRequest;
            $req->setFields("product_id,cid,props,name,pic_url,price");
            $req->setQ($title);
            $req->setPageNo($page_no);
            $req->setPageSize($page_size);
            $resp = $c->execute($req);
            $result = json_encode($resp);
            return $result;
        }
    }
?>