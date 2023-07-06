<?php 

declare(strict_types=1);

namespace App\Tests;

use App\ComissionCalculator;
use App\Contract\Service\BinProviderInterface;
use App\Contract\Service\ComissionRateProviderInterface;
use App\Contract\Service\EuCountryCheckerInterface;
use App\Contract\Service\ExchangeRateProviderInterface;
use App\Contract\Service\TransactionDataProviderInterface;
use App\Exception\DataProviderException;
use App\Response\DataFormatter;
use App\Service\EuCountryChecker;
use PHPUnit\Framework\TestCase;

final class ComissionCalculatorTest extends TestCase
{
    protected TransactionDataProviderInterface $dataProvider;

    protected EuCountryCheckerInterface $euCountryChecker;
    
    protected BinProviderInterface $binProvider;
    
    protected ExchangeRateProviderInterface $exchangeRateProvider;
    
    protected ComissionRateProviderInterface $comissionRateProvider;
    
    protected function setUp(): void
    {
        $this->dataProvider = $this->createMock(TransactionDataProviderInterface::class);
        $this->euCountryChecker = new EuCountryChecker();
        $this->binProvider = $this->createMock(BinProviderInterface::class);
        $this->exchangeRateProvider  = $this->createMock(ExchangeRateProviderInterface::class);
        $this->comissionRateProvider = $this->createMock(ComissionRateProviderInterface::class);
    }

    public function testComissionCalculator(): void
    {
        $this->dataProvider
            ->expects($this->any())
            ->method('fetchData')
            ->will($this->returnCallback(
                function () {
                    $data = [
                        [
                            "bin" => "45717360",
                            "amount" => "100.00",
                            "currency" => "EUR",
                        ],
                        [
                            "bin" => "516793",
                            "amount" => "50.00",
                            "currency" => "USD",
                        ],
                        [
                            "bin" => "45417360",
                            "amount" => "10000.00",
                            "currency" => "JPY",
                        ],
                    ];
                    foreach ($data as $e) {
                        yield $e;
                    }
                }
            )
        );

        $this->binProvider
            ->method("getBin")
            ->will(
                $this->onConsecutiveCalls(
                    [
                        "number" => [
                            "length" => 16,
                            "luhn" => true,
                        ],
                        "country" => [
                            "numeric" => 208,
                            "alpha2" => "DK",
                        ],
                    ],
                    [
                        "number" => [
                            "length" => 16,
                            "luhn" => true,
                        ],
                        "country" => [
                            "numeric" => 208,
                            "alpha2" => "USA",
                        ],
                    ],
                    [
                        "number" => [
                            "length" => 16,
                            "luhn" => true,
                        ],
                        "country" => [
                            "numeric" => 208,
                            "alpha2" => "JPY",
                        ],
                    ]
                )
            );

        $this->exchangeRateProvider
            ->expects($this->any())
            ->method('getExchangeRate')
            ->will(
                $this->onConsecutiveCalls(1, 1.23396, 132.360679)
            );

        $this->comissionRateProvider
            ->expects($this->any())
            ->method('getRate')
            ->will(
                $this->onConsecutiveCalls(0.01, 0.02, 0.02)
            );

        $calculator = new ComissionCalculator(
            $this->dataProvider,
            $this->euCountryChecker,
            $this->binProvider,
            $this->exchangeRateProvider,
            $this->comissionRateProvider,
        );

        $result = $calculator->calculate();

        $expected = [
            1,
            0.82,
            1.52,
        ];

        $dataFormatter = new DataFormatter();
        foreach ($result as $key => $value) {
            $this->assertEqualsWithDelta($expected[$key], $dataFormatter->format($value), 0.001);
        }
    }

    public function testUnableToFetchBin(): void
    {
        $this->dataProvider
            ->expects($this->any())
            ->method('fetchData')
            ->will($this->returnCallback(
                function () {
                    $data = [
                        [
                            "bin" => "45717360",
                            "amount" => "100.00",
                            "currency" => "EUR",
                        ]
                    ];
                    foreach ($data as $e) {
                        yield $e;
                    }
                }
            )
        );

        $this->binProvider
            ->expects($this->once())
            ->method("getBin")
            ->willThrowException(new DataProviderException('Unable to load BIN'));

        $calculator = new ComissionCalculator(
            $this->dataProvider,
            $this->euCountryChecker,
            $this->binProvider,
            $this->exchangeRateProvider,
            $this->comissionRateProvider,
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to load BIN');

        $calculator->calculate();
    }

    public function testUnableToFetchExchangeRate(): void
    {
        $this->dataProvider
            ->expects($this->any())
            ->method('fetchData')
            ->will($this->returnCallback(
                function () {
                    $data = [
                        [
                            "bin" => "45717360",
                            "amount" => "100.00",
                            "currency" => "EUR",
                        ]
                    ];
                    foreach ($data as $e) {
                        yield $e;
                    }
                }
            )
        );

        $this->binProvider
            ->method("getBin")
            ->will(
                $this->onConsecutiveCalls(
                    [
                        "number" => [
                            "length" => 16,
                            "luhn" => true,
                        ],
                        "country" => [
                            "numeric" => 208,
                            "alpha2" => "DK",
                        ],
                    ]
                )
            );

        $this->exchangeRateProvider
            ->expects($this->once())
            ->method("getExchangeRate")
            ->willThrowException(new DataProviderException('Unable to load exchange rates!'));

        $calculator = new ComissionCalculator(
            $this->dataProvider,
            $this->euCountryChecker,
            $this->binProvider,
            $this->exchangeRateProvider,
            $this->comissionRateProvider,
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to load exchange rates!');

        $calculator->calculate();
    }
}