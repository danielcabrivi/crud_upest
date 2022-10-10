<?php
namespace App\Controllers;

use Foundation\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    protected $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function index(){

        $this->getTokenAPI();

        $client_data = $this->client->getAll();

        return $this->render('client/index',[
            'client_data' => $client_data
        ]);
    }

    public function clientForm(){
        $myId = $this->getParam('myId');

        $client_data = null;

        if ($myId){
            $client_data = $this->client->getById($myId);
        }

        return $this->render('client/client_form',[
            'client_data' => $client_data
        ]);
    }

    public function clientSave(){
        $myId = $this->getParam('myId');

        $dados = $this->getDataForm();

        $resposta = $this->formValidation($dados);

        // Valida os dados do form no back-end
        if ($resposta['status']){
            // Se existir um ID o fluxo irá para alteração, caso contrário, será inserção
            if ($myId){
                if ($this-> client-> updateById($myId, $dados)) {
                    session()-> put('_sucesso', 'Registro aualizado com sucesso no Banco de Dados!');
                    // Cria o cliente na API
                    if($this->updateClientAPI($dados, $myId, $this->getTokenAPI()) == 200){
                        session()-> put('_sucesso_API', 'Registro alterado com sucesso na API');
                    }else{
                        session()-> put('_erro_API', 'Erro ao alterar o registro na API');
                    }
                } else {
                    session()-> put('_erro', 'Erro ao atualizar o registro no Banco de Dados!');
                }

            }else{
                $myId = $this->client->insert($dados);
                if ($myId){
                    session()-> put('_sucesso', 'Registro cadastrado com sucesso no Banco de Dados!');
                    // Cria o cliente na API
                    if($this->createClientAPI($dados, $myId, $this->getTokenAPI()) == 200){
                        session()-> put('_sucesso_API', 'Registro cadastrado com sucesso na API');
                    }else{
                        session()-> put('_erro_API', 'Erro ao cadastrar o registro na API');
                    }
                } else {
                    session()-> put('_erro', 'Erro ao cadastrar o registro no Banco de Dados!');
                }
            }

        }else{
            session()-> put('_erro', $resposta['msg']);
        }

        return redirect()-> route('index');
    }

    public function deleteClient(){
        $myId = $this->getParam('myId');

        if ($myId){
            if ($this->client->deleteById($myId)){
                session()->put('_sucesso', 'Registro excluído com sucesso no Banco de Dados');

                if($this->deleteClientAPI($myId, $this->getTokenAPI()) == 200){
                    session()-> put('_sucesso_API', 'Registro excluído com sucesso na API');
                }else{
                    session()-> put('_erro_API', 'Erro ao excluir o registro na API');
                }

            } else {
                session()->put('_erro', 'Erro ao excluir o registro no Banco de Dados');
            }
        }

        return redirect()-> route('index');
    }

    private function getDataForm(){

        $data = [
            'name' => empty(input()->get('name')) ? null : input()->get('name'),
            'document' => empty(input()->get('document')) ? null : input()->get('document'),
            'email1' => empty(input()->get('email1')) ? null : input()->get('email1'),
            'email2' => empty(input()->get('email2')) ? null : input()->get('email2'),
            'phone1' => empty(input()->get('phone1')) ? null : input()->get('phone1'),
            'phone2' => empty(input()->get('phone2')) ? null : input()->get('phone2'),
            'InvoiceHoldIss' => !is_null(input()->get('InvoiceHoldIss')),
            'municipalDocument' => empty(input()->get('municipalDocument')) ? null : input()->get('municipalDocument'),
            'zipCode' => empty((int) input()->get('zipCode')) ? null : (int) input()->get('zipCode'),
            'street' => empty(input()->get('street')) ? null : input()->get('street'),
            'number' => empty(input()->get('number')) ? null : input()->get('number'),
            'complement' => empty(input()->get('complement')) ? null : input()->get('complement'),
            'neighborhood' => empty(input()->get('neighborhood')) ? null : input()->get('neighborhood'),
            'city' => empty(input()->get('city')) ? null : input()->get('city'),
            'state' => empty(input()->get('state')) ? null : input()->get('state'),
            'tagName' => empty(input()->get('tagName')) ? null : input()->get('tagName'),
            'tagValue' => empty(input()->get('tagValue')) ? null : input()->get('tagValue')
        ];

        return $data;
    }

    private function formValidation($data){
        $count = 0;
        $msg = '';

        if (is_null($data['name']) || is_null($data['email1']) || is_null($data['street']) || is_null($data['number']) ||
            is_null($data['neighborhood']) || is_null($data['city']) || is_null($data['document']) || is_null($data['zipCode']) || is_null($data['state']))
        {
            $msg .= "Os campos obrigatórios devem estar preenchidos!\n";
            $count++;
        }

        if (
            (!is_numeric($data['phone1']) && !is_null($data['phone1'])) ||
            (!is_numeric($data['phone2']) && !is_null($data['phone2']))
        ){
            $msg .= "Informe apenas números para o campo de telefone!";
            $count++;
        }

        if (!is_numeric($data['zipCode'])){
            $msg = "O campo CEP deve conter apenas números!\n";
            $count++;
        }

        if (is_numeric($data['document'])){
            if(strlen($data['document']) == 11) {
                if(!$this->validaCpf($data['document'])){
                    $msg .= "O CPF inválido!\n";
                    $count++;
                }
            } elseif(strlen($data['document']) == 14) {

                if(!$this->validaCnpj($data['document'])){
                    $msg .= "O CPF inválido!\n";
                    $count++;
                }
            }else{
                $msg .= "O CPF/CNPJ inválido!\n";
                $count++;
            }
        }else{
            $msg .= "O CPF/CNPJ deve conter apenas números!\n";
            $count++;
        }

        // Verifica se algum campo não passou na validação
        if ($count > 0){
            $result = [
                'status' => false,
                'msg' => $msg
            ];
        }else{
            $result = [
                'status' => true,
                'msg' => 'Dados validados com sucesso'
            ];
        }

        return $result;
    }

    private function getTokenAPI(){
        $curl = curl_init();
        $galaxId = "5473";
        $galaxHash = "83Mw5u8988Qj6fZqS4Z8K7LzOo1j28S706R0BeFe";

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sandbox.cloud.galaxpay.com.br/v2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "grant_type": "authorization_code",
                "scope": "customers.read customers.write plans.read plans.write transactions.read transactions.write webhooks.write cards.read cards.write card-brands.read subscriptions.read subscriptions.write charges.read charges.write boletos.read"
            }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode( $galaxId . ':' . $galaxHash),
                'Content-Type: application/json'
            ),
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response->access_token;
    }

    private function generateJsonData($data, $myId)
    {
        $phones = null;

        // mapeamento do endereço
        $Address = [
            "zipCode"=> $data['zipCode'],
            "street"=> $data['street'],
            "number"=> $data['number'],
            "complement"=> $data['complement'],
            "neighborhood"=> $data['neighborhood'],
            "city"=> $data['city'],
            "state"=> $data['state']
        ];

        $result = [
            "myId" => "MYID_DANIEL_".$myId,
            "name" => $data['name'],
            "document" => $data['document'],
            "invoiceHoldIss" => $data['InvoiceHoldIss'],

            "Address" => $Address
        ];

        // mapeamento do e-mail
        if (!empty($data['email2'])){
            $emails =[
                $data['email1'] ,
                $data['email2']];
        }else{
            $emails = [$data['email1']];
        }

        // mapeamento do telefone
        if (!is_null($data['phone1'])){
            $phones[] = intval($data['phone1']);
        }

        if (!is_null($data['phone2'])){
            $phones[] = intval($data['phone2']);
        }

        if (!is_null($phones)){
            $result["phones"] = $phones;
        }

        $result["emails"] = $emails;

        $data_json = json_encode($result);

        return $data_json;
    }

    private function createClientAPI($data, $myId, $access_token){

        $data_json = $this->generateJsonData($data, $myId);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sandbox.cloud.galaxpay.com.br/v2/customers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$access_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $this->saveLog($data_json, "POST", "REQUEST");
        $this->saveLog($response, "POST", "RESPONSE");

        return $status_code;
    }

    private function updateClientAPI($data, $myId, $access_token){

        $data_json = $this->generateJsonData($data, $myId);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sandbox.cloud.galaxpay.com.br/v2/customers/MYID_DANIEL_'.$myId.'/myId',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$access_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $this->saveLog($data_json, "PUT", "REQUEST");
        $this->saveLog($response, "PUT", "RESPONSE");

        return $status_code;
    }

    private function deleteClientAPI($myId, $access_token){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sandbox.cloud.galaxpay.com.br/v2/customers/MYID_DANIEL_'.$myId.'/myId',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => [],
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$access_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $this->saveLog('https://api.sandbox.cloud.galaxpay.com.br/v2/customers/MYID_DANIEL_'.$myId.'/myId', "DELETE", "REQUEST");
        $this->saveLog($response, "DELETE", "RESPONSE");

        return $status_code;
    }

    private function validaCnpj($cnpj) {
        // Deixa o CNPJ com apenas números
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Garante que o CNPJ é uma string
        $cnpj = (string) $cnpj;

        // O valor original
        $cnpj_original = $cnpj;

        // Captura os primeiros 12 números do CNPJ
        $primeiros_numeros_cnpj = substr($cnpj, 0, 12);

        /**
         * Multiplicação do CNPJ
         *
         * @param string $cnpj Os digitos do CNPJ
         * @param int $posicoes A posição que vai iniciar a regressão
         * @return int O
         *
         */
        if (!function_exists('multiplica_cnpj')) {

            function multiplica_cnpj($cnpj, $posicao = 5) {
                // Variável para o cálculo
                $calculo = 0;

                // Laço para percorrer os item do cnpj
                for ($i = 0; $i < strlen($cnpj); $i++) {
                    // Cálculo mais posição do CNPJ * a posição
                    $calculo = $calculo + ( $cnpj[$i] * $posicao );

                    // Decrementa a posição a cada volta do laço
                    $posicao--;

                    // Se a posição for menor que 2, ela se torna 9
                    if ($posicao < 2) {
                        $posicao = 9;
                    }
                }
                // Retorna o cálculo
                return $calculo;
            }

        }

        // Faz o primeiro cálculo
        $primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);

        // Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
        // Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
        $primeiro_digito = ( $primeiro_calculo % 11 ) < 2 ? 0 : 11 - ( $primeiro_calculo % 11 );

        // Concatena o primeiro dígito nos 12 primeiros números do CNPJ
        // Agora temos 13 números aqui
        $primeiros_numeros_cnpj .= $primeiro_digito;

        // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
        $segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
        $segundo_digito = ( $segundo_calculo % 11 ) < 2 ? 0 : 11 - ( $segundo_calculo % 11 );

        // Concatena o segundo dígito ao CNPJ
        $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

        // Verifica se o CNPJ gerado é idêntico ao enviado
        if ($cnpj === $cnpj_original) {
            return true;
        }else {
            return false;
        }
    }

    private function validaCpf( $cpf = false ) {
        /**
         * Multiplica dígitos vezes posições
         *
         * @param string $digitos Os digitos desejados
         * @param int $posicoes A posição que vai iniciar a regressão
         * @param int $soma_digitos A soma das multiplicações entre posições e dígitos
         * @return int Os dígitos enviados concatenados com o último dígito
         *
         */
        if ( ! function_exists('calc_digitos_posicoes') ) {
            function calc_digitos_posicoes( $digitos, $posicoes = 10, $soma_digitos = 0 ) {
                // Faz a soma dos dígitos com a posição
                // Ex. para 10 posições:
                //   0    2    5    4    6    2    8    8   4
                // x10   x9   x8   x7   x6   x5   x4   x3  x2
                //   0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
                for ( $i = 0; $i < strlen( $digitos ); $i++  ) {
                    $soma_digitos = $soma_digitos + ( $digitos[$i] * $posicoes );
                    $posicoes--;
                }

                // Captura o resto da divisão entre $soma_digitos dividido por 11
                // Ex.: 196 % 11 = 9
                $soma_digitos = $soma_digitos % 11;

                // Verifica se $soma_digitos é menor que 2
                if ( $soma_digitos < 2 ) {
                    // $soma_digitos agora será zero
                    $soma_digitos = 0;
                } else {
                    // Se for maior que 2, o resultado é 11 menos $soma_digitos
                    // Ex.: 11 - 9 = 2
                    // Nosso dígito procurado é 2
                    $soma_digitos = 11 - $soma_digitos;
                }

                // Concatena mais um dígito aos primeiro nove dígitos
                // Ex.: 025462884 + 2 = 0254628842
                $cpf = $digitos . $soma_digitos;

                // Retorna
                return $cpf;
            }
        }

        // Verifica se o CPF foi enviado
        if ( ! $cpf ) {
            return false;
        }

        // Remove tudo que não é número do CPF
        // Ex.: 025.462.884-23 = 02546288423
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

        // Verifica se o CPF tem 11 caracteres
        // Ex.: 02546288423 = 11 números
        if ( strlen( $cpf ) != 11 ) {
            return false;
        }

        // Captura os 9 primeiros dígitos do CPF
        // Ex.: 02546288423 = 025462884
        $digitos = substr($cpf, 0, 9);

        // Faz o cálculo dos 9 primeiros dígitos do CPF para obter o primeiro dígito
        $novo_cpf = calc_digitos_posicoes( $digitos );

        // Faz o cálculo dos 10 dígitos do CPF para obter o último dígito
        $novo_cpf = calc_digitos_posicoes( $novo_cpf, 11 );

        // Verifica se o novo CPF gerado é idêntico ao CPF enviado
        if ( $novo_cpf === $cpf ) {
            // CPF válido
            return true;
        } else {
            // CPF inválido
            return false;
        }
    }

    private function saveLog($data, $mt, $type){

        $file = fopen($_SERVER['DOCUMENT_ROOT'].'/public/app/log/requests.log','a');

        $content = "[".date("d-m-Y H:i:s")."] - ".$type.": ".$mt." - JSON: ". $data. "\n";

        fwrite($file, $content);

        fclose($file);
    }
}
