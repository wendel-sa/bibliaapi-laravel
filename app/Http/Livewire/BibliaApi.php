<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BibliaApi extends Component
{
    public $message;
    public $versoes;
    public $livros;
    public $testamentos;
    public $versiculos;
    public $capitulos;
    public $NomeLivroAtual;
    public $idLivroAtual;
    public $NomeVersaoAtual;
    public $idVersaoAtual;
    public $NumeroCapAtual;
    public $qtdCapitulos;
    public $QtdVersiculos;
    public $cap = 1;
    public $versaoSelecionada = null;
    public $livroSelecionado;
    public $capituloSelecionado;
    public $abbrev  = null;
    public $detalhesLivro;
    public $palavra = '';

    public function boot()
    {
        $token = $this->userApi();
        //ficar sempre enviando o token do usuario para o servidor da biblia
        $this->versoes = $this->getVersoes($token);
    }

    public function userApi()
    {

        $urlUserStas = 'https://www.abibliadigital.com.br/api/users/token';

        $data2 = [
            'email' => auth()->user()->email,
            'password' => auth()->user()->password,
        ];

        $data_string2 = json_encode($data2);

        $ch2 = curl_init($urlUserStas);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_string2);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch2,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string2)
            )
        );

        $userApi = curl_exec($ch2);
        $userApi = json_decode($userApi, true);
        //se o servidor retornar um token
        if (isset($userApi['token'])) {
            $token = $userApi['token'];
        } else {
            $token = null;
        }


        if ($token) {

            $url3 = 'https://www.abibliadigital.com.br/api/users/' . auth()->user()->email;

            $ch3 = curl_init($url3);
            curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch3,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                )
            );
            $result3 = curl_exec($ch3);
            $result3 = json_decode($result3, true);
            $token = $result3['token'];
        } else {
            $url = 'https://www.abibliadigital.com.br/api/users';
            $data = [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'password' => auth()->user()->password,
                'notifications' => true
            ];
            $data_string = json_encode($data);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string)
                )
            );
            $result = curl_exec($ch);
            $result = json_decode($result, true);
            $token = $result['token'];
        }
        return $token;
    }

    //requisição assincrona para pegar as versões e os livros da api
    public function mount()
    {
        $this->userApi();
        $this->versoes = $this->getVersoes($this->userApi());
        $this->livros = $this->getLivros($this->userApi());
        $this->detalhesLivro = $this->getDetalhesLivro($this->userApi());
        $this->versiculos = $this->getVersiculos($this->userApi());
    }



    public function getVersoes($token)
    {
        $url = 'https://www.abibliadigital.com.br/api/versions';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            )
        );
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        return $result;
    }

    public function getLivros($token)
    {
        $url = 'https://www.abibliadigital.com.br/api/books';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            )
        );
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        return $result;
    }


    public function getDetalhesLivro($token)
    {
        $url = 'https://www.abibliadigital.com.br/api/books/' . $this->livroSelecionado;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            )
        );
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $this->qtdCapitulos = $result[0]['chapters'];
        return $result[0];
    }

    public function getVersiculos($token)
    {
        $url = 'https://www.abibliadigital.com.br/api/verses/nvi/gn/1';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            )
        );
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        return $result;
    }

    public function  updatingAbbrev($abbrev)
    {
        $token = $this->userApi();
        //valida se a versao foi selecionada
        if ($this->versaoSelecionada == null) {
            $this->versaoSelecionada = 'acf';
        }

        $this->cap = 1;

        $url = 'https://www.abibliadigital.com.br/api/books/' . $abbrev;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            )
        );
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $this->detalhesLivro = $result;

        $url2 = 'https://www.abibliadigital.com.br/api/verses/' . $this->versaoSelecionada . '/' . $abbrev . '/' . $this->cap;

        $ch2 = curl_init($url2);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch2,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            )
        );

        $result2 = curl_exec($ch2);
        $result2 = json_decode($result2, true);
        $this->versiculos = $result2;
        $this->qtdCapitulos = $this->detalhesLivro['chapters'];
        return $abbrev;
    }

    public function  updatingVersaoSelecionada($versaoSelecionada)
    {

        if ($this->abbrev == null) {
            $this->abbrev = 'gn';
        }


        $this->cap = 1;


        $this->userApi();
        $url = 'https://www.abibliadigital.com.br/api/verses/' . $versaoSelecionada . '/' . $this->abbrev . '/' . $this->cap;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->userApi()
            )
        );
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $this->versiculos = $result;
        return $versaoSelecionada;
    }


    public function  updatingCap($cap)
    {

        if ($this->abbrev == null) {
            $this->abbrev = 'gn';
        }

        if ($this->versaoSelecionada == null) {
            $this->versaoSelecionada = 'acf';
        }

        $this->userApi();
        $url = 'https://www.abibliadigital.com.br/api/verses/' . $this->versaoSelecionada . '/' . $this->abbrev . '/' . $cap;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->userApi()
            )
        );
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $this->versiculos = $result;
        return $cap;
    }

    public function updatingPalavra($palavra)
    {
        $this->userApi();
        if ($this->versaoSelecionada == null) {
            $this->versaoSelecionada = 'acf';
        }
        //Endpoint: POST https://www.abibliadigital.com.br/api/verses/search
        $url = 'https://www.abibliadigital.com.br/api/verses/search';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            'version' => $this->versaoSelecionada,
            'search' => $palavra
        )));

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->userApi()
            )
        );

        $result = curl_exec($ch);
        $result = json_decode($result, true);
        dd($result);
        $this->versiculos = $result;
        return $palavra;
    }

    public function render()
    {
        return view('livewire.biblia-api');
    }
}
