<?php
namespace Addons\Core\Http;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SerializableResponse implements \Serializable {

	private $response;

	public function __construct($response = null)
	{
        $this->response = $response;
	}

	/**
     * Serializes the response
     *
     * @return string|null
     * @link http://php.net/manual/en/serializable.serialize.php
     */
    public function serialize()
    {
    	return serialize($this->data());
    }

    /**
     * Unserializes the response.
     *
     *
     * @param string $serialized
     *
     * @throws Exception
     * @link http://php.net/manual/en/serializable.unserialize.php
     */
    public function unserialize($serialized)
    {
    	$data = unserialize($serialized);
    	return $this->invoke($data);
    }

    public function data()
    {
        if (is_scalar($this->response) || $this->response instanceof View)
             return [ 
                'content' => strval($this->response),
                'status' => 200,
                'headers' => [],
            ]; 
        elseif ($this->response instanceof Response)
            return [
                'content' => $this->response->content(),
                'status' => $this->response->status(),
                'headers' => $this->response->headers->all(),
            ];
        else
            return null;
    	
    }

    public function invoke($data)
    {
        return !empty($data) ? Response::create($data['content'], $data['status'], $data['headers']) : null;
    }
}