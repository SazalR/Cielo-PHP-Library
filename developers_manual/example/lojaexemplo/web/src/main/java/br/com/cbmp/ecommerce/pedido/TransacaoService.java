package br.com.cbmp.ecommerce.pedido;

import br.com.cbmp.ecommerce.contexto.Destino;
import br.com.cbmp.ecommerce.contexto.Loja;
import br.com.cbmp.ecommerce.requisicao.Mensagem;
import br.com.cbmp.ecommerce.requisicao.MensagemAutorizacao;
import br.com.cbmp.ecommerce.requisicao.MensagemAutorizacaoDireta;
import br.com.cbmp.ecommerce.requisicao.MensagemCancelamento;
import br.com.cbmp.ecommerce.requisicao.MensagemCaptura;
import br.com.cbmp.ecommerce.requisicao.MensagemConsulta;
import br.com.cbmp.ecommerce.requisicao.MensagemNovaTransacao;
import br.com.cbmp.ecommerce.requisicao.MensagemTid;
import br.com.cbmp.ecommerce.requisicao.Requisicao;
import br.com.cbmp.ecommerce.resposta.FalhaComunicaoException;
import br.com.cbmp.ecommerce.resposta.Transacao;

public class TransacaoService {
	
	private Loja loja;
	
	private Destino destino = new Destino();
	
	public TransacaoService(final Loja loja) {
		this.loja = loja;
	}	
	
	public Transacao criarTransacao(Pedido pedido) throws FalhaComunicaoException {
		Mensagem mensagem = new MensagemNovaTransacao(loja, pedido);
		Requisicao requisicao = new Requisicao(mensagem);
		return requisicao.enviarPara(destino);
	}

	public Transacao capturar(Transacao transacao, long valor) throws FalhaComunicaoException {
		Mensagem mensagem = new MensagemCaptura(loja, transacao, valor);
		Requisicao requisicao = new Requisicao(mensagem);
		return requisicao.enviarPara(destino);
	}
	
	public Transacao cancelar(Transacao transacao) throws FalhaComunicaoException {
		Mensagem mensagem = new MensagemCancelamento(loja, transacao);
		Requisicao requisicao = new Requisicao(mensagem);
		return requisicao.enviarPara(destino);
	}

	
	public Transacao autorizarDireto(Pedido pedido) throws FalhaComunicaoException {
		MensagemTid mensagemTid = new MensagemTid(loja, pedido.getFormaPagamento());
		Requisicao requisicaoTid = new Requisicao(mensagemTid);
		String tid = requisicaoTid.enviarPara(destino).getTid();
		
		MensagemAutorizacaoDireta mensagemAutorizacaoDireta = 
			new MensagemAutorizacaoDireta(loja)
				.setPedido(pedido)
				.setTid(tid);
		Requisicao requisicaoAutorizacaoDireta = new Requisicao(mensagemAutorizacaoDireta);
		
		return requisicaoAutorizacaoDireta.enviarPara(destino);
	}
	
	
	public Transacao autorizar(Transacao transacao) throws FalhaComunicaoException {
		Mensagem mensagem = new MensagemAutorizacao(loja, transacao);
		Requisicao requisicao = new Requisicao(mensagem);
		return requisicao.enviarPara(destino);
	}

	public Transacao consultar(Transacao transacao) throws FalhaComunicaoException {
		Mensagem mensagem = new MensagemConsulta(loja, transacao);
		Requisicao requisicao = new Requisicao(mensagem);
		return requisicao.enviarPara(destino);
	}
	
}