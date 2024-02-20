<?php
namespace App\Vinnies;

use Swap\Builder;
use Money\Currency;
use Money\Converter;
use Money\Money as BaseMoney;
use InvalidArgumentException;
use Money\Exchange\SwapExchange;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use AmrShawky\LaravelCurrency\Facade\Currency as ExchangeRate;

class Money
{
    protected $currencies;
    protected $currency;
    protected $money;
    protected $swap;
    protected $exchange;
    protected $converter;

    public function __construct($value = 0, $currencyCode = 'AUD')
    {
        $this->currencies = new ISOCurrencies();
        $this->currency   = new Currency($currencyCode);
        $this->money      = new BaseMoney(Helper::formatDecimal($value * 100), $this->currency); // value needs to be in cent
        $this->swap       = (new Builder())->add('fixer', ['access_key' => '780b7d308846371e075da17536864c03'])->add('abstract_api', ['api_key' => '06f650d2876e44f8ac2e12a8c041b0c9'])->build();
        $this->exchange   = new SwapExchange($this->swap);
        $this->converter  = new Converter($this->currencies, $this->exchange);
    }

    public function __call($method, array $arguments)
    {
        if (!method_exists($this->money, $method)) {
            throw new InvalidArgumentException('Invalid method supplied: ' . $method);
        }

        $result = call_user_func_array([$this->money, $method], $arguments);

        return new self(
            $result->getAmount() / 100,
            $result->getCurrency()->getCode()
        );
    }

    public function instance()
    {
        return $this->money;
    }

    public function toAUD()
    {
        return $this->converter->convert($this->money, new Currency('AUD'));
    }

    public function getExchangeRate()
    {
        $exRate = ExchangeRate::rates()
        ->latest()
        ->base($this->currency->getCode())
        ->amount(1)
        ->round(4)
        ->get();

        return $exRate['AUD'];
    }

    public function value($money = false)
    {
        $moneyFormatter = new DecimalMoneyFormatter($this->currencies);

        return $moneyFormatter->format($money ?: $this->money);
    }
}
