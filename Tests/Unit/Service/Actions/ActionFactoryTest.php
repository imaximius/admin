<?php

namespace Araneum\AdminBundle\Tests\Unit\Service\Actions;

use Araneum\AdminBundle\Service\Actions\ActionBuilder;
use Araneum\AdminBundle\Service\Actions\ActionBuilderInterface;

/**
 * Class ActionBuilderTest
 *
 * @package Araneum\AdminBundle\Tests\Unit\Service\Actions
 */
class ActionBuilderTest extends \PHPUnit_Framework_TestCase
{
    const TEST_DELETE_LOCALE_ACTION_ROUTE = 'deleteLocaleActionRoute';
    const TEST_GENERATED_URL              = 'generatedUrl';

    protected $expectedAddOneAction                                = [
        "row" => [
            "deleteGroup" => [
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
            ],
        ],
        "top" => [
            "deleteGroup" => [
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
            ],
        ],
    ];
    protected $expectedAddTwoActionSameGroup                       = [
        "row" => [
            "deleteGroup" => [
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
            ],
        ],
        "top" => [
            "deleteGroup" => [
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
            ],
        ],
    ];
    protected $expectedAddTwoActionDifferentGroupDifferentPosition = [
        "row" => [
            "deleteGroup" => [
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
            ],
        ],
        "top" => [
            "deleteGroup" => [
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
            ],
            "addGroup" => [
                [
                    "resource" => self::TEST_GENERATED_URL,
                ],
            ],
        ],
    ];

    /**
     * @var  ActionBuilder
     */
    private $actionBuilder;

    /**
     * Set Up method
     */
    public function setUp()
    {
        $routerMock = $this->getMockBuilder('\Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $routerMock
            ->method('generate')
            ->will($this->returnValue('generatedUrl'));

        $this->actionBuilder = new ActionBuilder($routerMock);
    }

    /**
     * Data source for add method
     *
     * @return array
     */
    public function addDataSource()
    {
        return [
            'one action normal return' => [
                [
                    [
                        'group' => 'deleteGroup',
                        'options' => [
                            'resource' => self::TEST_DELETE_LOCALE_ACTION_ROUTE,
                            'position' => ActionBuilderInterface::POSITION_ALL,

                        ],
                    ],
                ],
                $this->expectedAddOneAction,
            ],
            'two action same group normal return' => [
                [
                    [
                        'group' => 'deleteGroup',
                        'options' => [
                            'resource' => self::TEST_DELETE_LOCALE_ACTION_ROUTE,
                            'position' => ActionBuilderInterface::POSITION_ALL,

                        ],
                    ],
                    [
                        'group' => 'deleteGroup',
                        'options' => [
                            'resource' => self::TEST_DELETE_LOCALE_ACTION_ROUTE,
                            'position' => ActionBuilderInterface::POSITION_ALL,

                        ],
                    ],
                ],
                $this->expectedAddTwoActionSameGroup,
            ],
            'two action different group different position normal return' => [
                [
                    [
                        'group' => 'deleteGroup',
                        'options' => [
                            'resource' => self::TEST_DELETE_LOCALE_ACTION_ROUTE,
                            'position' => ActionBuilderInterface::POSITION_ALL,

                        ],
                    ],
                    [
                        'group' => 'addGroup',
                        'options' => [
                            'resource' => self::TEST_DELETE_LOCALE_ACTION_ROUTE,
                            'position' => ActionBuilderInterface::POSITION_TOP,

                        ],
                    ],
                ],
                $this->expectedAddTwoActionDifferentGroupDifferentPosition,
            ],
        ];
    }

    /**
     * Test add method
     *
     * @dataProvider addDataSource
     *
     * @param array $actions
     * @param array $expected
     */
    public function testAdd($actions, $expected)
    {
        foreach ($actions as $action) {
            $this->actionBuilder->add($action['group'], $action['options']);
        }

        $this->assertEquals($expected, $this->actionBuilder->getActions());
    }

    /**
     * Test add method with no specified position expect exception
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddNoPositionException()
    {
        $this->actionBuilder->add(
            'deleteGroup',
            [
                'resource' => self::TEST_DELETE_LOCALE_ACTION_ROUTE,
            ]
        );
    }

    /**
     * Test add method with not valid position expect exception
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddNotValidPositionException()
    {
        $this->actionBuilder->add(
            'deleteGroup',
            [
                'resource' => self::TEST_DELETE_LOCALE_ACTION_ROUTE,
                'position' => 'wrongPosition',
            ]
        );
    }
}
