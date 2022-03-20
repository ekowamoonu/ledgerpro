<?php ob_start();


if(isset($_COOKIE['saleslog']))
{
		setcookie("saleslog","",time()-10);
        header("Location: index");
}

else if(isset($_COOKIE['pdlog'])){
   	setcookie("pdlog","",time()-10);
     header("Location: index");
}

else if(isset($_COOKIE['storelog'])){
		setcookie("storelog","",time()-10);
        header("Location: index");
	
}

else if(isset($_COOKIE['acclog'])){
		setcookie("acclog","",time()-10);
        header("Location: index");
}

else if (isset($_COOKIE['bosslog'])){
	   setcookie("bosslog","",time()-10);
        header("Location: index");
}

?>