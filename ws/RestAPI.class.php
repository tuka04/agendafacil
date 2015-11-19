<?php
/**
 * http://code.tutsplus.com/tutorials/a-beginners-guide-to-http-and-rest--net-16340
 */
class RestAPI {
	
	private $resource_allowed;
	
	public function __construct(){
		require_once PATH_ROOT.'ws/Server.class.php';
		/* aqui vao os serviços liberados no webservice*/
		$this->resource_allowed=new ArrayObj(array("usuario","login"
												 ,"especialidade"
												 ,"especialidade_usuario"
												
		));
		
		$paths=Server::getURIPath();//uri path com recursos e variaveis
		$resource=Server::getResource($paths);//recursos a partir do uri path
		//buscar pelo recurso
		if (!is_null($this->resource_allowed->search($resource))) {
			
			$object=$this->getObject($resource);
			
			$vars=Server::getVars($paths);//vars a partir do uri path
			$method=Server::getMethod();//metodo
			
			$this->handle($method, $object, $vars);//gerencie o recurso
		} else {//recurso nao existe
			HTTPHeader::set(HTTPResponseCode::NOT_FOUND);
		}
	}
	
	private function handle($method,$object,$vars){
		$ret=new ArrayObj();
		switch ($method){
			case 'PUT':
				try{
					$object->put($vars);
				}catch (Exception $e){
					Erro::display($e->getMessage());
					return;
				}
				HTTPHeader::set(HTTPResponseCode::NO_CONTENT);
				break;
			case 'POST':
				try{
					$ret->append($object->post($vars));
				}catch (Exception $e){
					Erro::display($e->getMessage());
					return;
				}
				HTTPHeader::set(HTTPResponseCode::CREATED);
				HTTPHeader::setReponseJson();
				echo $ret->toJson();
				break;
			case 'DELETE':
				try{
					$object->delete($vars);
				}catch (Exception $e){
					Erro::display($e->getMessage());
					return;
				}
				HTTPHeader::set(HTTPResponseCode::NO_CONTENT);
				break;
			case 'GET':
				try{
					$ret=$object->get($vars);
				}
				catch (ValidatorException $e){
					Erro::display($e->getMessage());
					return;
				}
				catch (Exception $e){
					Erro::display($e->getMessage());
					return;
				}
				HTTPHeader::set(HTTPResponseCode::OK);
				HTTPHeader::setReponseJson();
				echo $ret->toJson();
				break;
			default:
				HTTPHeader::set(HTTPResponseCode::METHOD_NOT_ALLOWED);
				break;
		}
	}

	private function getObject($resource){
		if(stripos($resource,"_")!==false){
			$aux=explode("_",$resource);
			$obj_name="";
			foreach ($aux as $v)
				$obj_name.=ucfirst($v);
		}
		else
			$obj_name=ucfirst($resource);
		require_once PATH_ROOT."model/".$obj_name.".class.php";
		
		return new $obj_name;
	}

}


abstract class HTTPResponseCode {
	/* http 20X */
	const OK=200;
	const CREATED=201;
	const NO_CONTENT=204;
	/* http 40X */
	const BAD_REQUEST=400;
	const NOT_FOUND=404;
	const UNAUTHORIZED=401;
	const METHOD_NOT_ALLOWED=405;
	const CONFLICT=409;
	/* http 50X */
	const INTERNAL_SERVER_ERROR=500;
	
}

final class HTTPHeader {
	static public function set($c,$msg=""){
		switch ($c){

			case HTTPResponseCode::OK:
				header('HTTP/1.1 200 OK');
				break;
			case HTTPResponseCode::CREATED:
				header('HTTP/1.1 201 Created');
				break;
			case HTTPResponseCode::NO_CONTENT:
				header('HTTP/1.1 204 No Content');
				break;
			case HTTPResponseCode::NOT_FOUND:
				header('HTTP/1.1 404 '."Serviço não encontrado");
				break;
			case HTTPResponseCode::INTERNAL_SERVER_ERROR:
				header('HTTP/1.1 500 '.$msg);
				break;
			default:
				header('HTTP/1.1 404 Not Found');
				break;
		}
	}
	static public function setResponseType($type,$charset="UTF-8"){
		switch ($type){
			case "json":
				header("Content-Type: application/json; charset=".$charset);
				break;
			default:
				header("Content-Type: application/json; charset=".$charset);
				break;
		}
		
	}
	
	static public function setReponseJson(){
		self::setResponseType("json");
	}
}

?>