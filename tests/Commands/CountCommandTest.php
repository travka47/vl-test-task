<?php
namespace Test\Commands;

use App\Commands\CountCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CountCommandTest extends TestCase {

    private CONST ROOT_DIR = __DIR__ . '/../..';
    private CONST TEST_DIR = self::ROOT_DIR . '/test';

    private static Command $command;
    private static CommandTester $commandTester;
    private static Filesystem $filesystem;

    public static function setUpBeforeClass() : void
    {
        $app = new Application();
        $app->add(new CountCommand());

        self::$command = $app->find('count');
        self::$commandTester = new CommandTester(self::$command);
        self::$filesystem = new Filesystem();
    }


    public function testNoCountFilesInDirectory() : void
    {
        mkdir(self::TEST_DIR);
        file_put_contents(self::TEST_DIR . '/test.txt', -20.02);

        self::$commandTester->execute([
            'directory' => self::TEST_DIR,
            '-t' => true
        ]);

        unlink(self::TEST_DIR . '/test.txt');
        rmdir(self::TEST_DIR);

        $output = self::$commandTester->getDisplay();
        $this->assertEquals('There are no count files in this directory', trim($output));
    }

    public function testUnableDirectory() : void
    {
        self::$commandTester->execute([
            'directory' => self::TEST_DIR
        ]);

        $output = self::$commandTester->getDisplay();
        $this->assertStringContainsString('Unable directory, sum will be calculated for the root', trim($output));
    }

    public function testTablePrinting() : void
    {
        mkdir(self::TEST_DIR);
        file_put_contents(self::TEST_DIR . '/count', '20.02');

        self::$commandTester->execute([
            'directory' => self::TEST_DIR,
            '-t' => true
        ]);

        unlink(self::TEST_DIR . '/count');
        rmdir(self::TEST_DIR);

        $output = self::$commandTester->getDisplay();
        $this->assertStringContainsString('│ File  │ Sum   │', trim($output));
    }

    public function testNegativeFloat() : void
    {
        mkdir(self::TEST_DIR);
        file_put_contents(self::TEST_DIR . '/count', -20.02);

        self::$commandTester->execute([
            'directory' => self::TEST_DIR
        ]);

        unlink(self::TEST_DIR . '/count');
        rmdir(self::TEST_DIR);

        $output = self::$commandTester->getDisplay();
        $this->assertEquals('Total: -20.02', trim($output));
    }

    public function testArrayOfNumbers() : void
    {
        mkdir(self::TEST_DIR);
        file_put_contents(self::TEST_DIR . '/count', '-12 1000 78.10 -10.0101');

        self::$commandTester->execute([
            'directory' => self::TEST_DIR
        ]);

        unlink(self::TEST_DIR . '/count');
        rmdir(self::TEST_DIR);

        $output = self::$commandTester->getDisplay();
        $this->assertEquals('Total: 1056.0899', trim($output));
    }

    public function testArrayOfNumbersWithDifferentSeparators() : void
    {
        mkdir(self::TEST_DIR);
        file_put_contents(self::TEST_DIR . '/count', '-12, 1000; 78.10   -10.0101');

        self::$commandTester->execute([
            'directory' => self::TEST_DIR
        ]);

        unlink(self::TEST_DIR . '/count');
        rmdir(self::TEST_DIR);

        $output = self::$commandTester->getDisplay();
        $this->assertEquals('Total: 1056.0899', trim($output));
    }

    public function testInvalidData() : void
    {
        mkdir(self::TEST_DIR);
        file_put_contents(self::TEST_DIR . '/count', 'test 56. .9 1.2.3 +14 100f f100 f100f');

        self::$commandTester->execute([
            'directory' => self::TEST_DIR
        ]);

        unlink(self::TEST_DIR . '/count');
        rmdir(self::TEST_DIR);

        $output = self::$commandTester->getDisplay();
        $this->assertEquals('Total: 0', trim($output));
    }

    public function testValidAndInvalidData() : void
    {
        mkdir(self::TEST_DIR);
        file_put_contents(self::TEST_DIR . '/count', 'test 56. 100000 .9 23456 1.2.3+14 100f f100 f100f');

        self::$commandTester->execute([
            'directory' => self::TEST_DIR
        ]);

        unlink(self::TEST_DIR . '/count');
        rmdir(self::TEST_DIR);

        $output = self::$commandTester->getDisplay();
        $this->assertEquals('Total: 123456', trim($output));
    }

    private function countTotal($searchDirectory): array
    {
        $files = [];
        $total = 0;

        $finder = new Finder();
        $finder->directories()->in($searchDirectory)->exclude('vendor');

        $rootCountFile = $searchDirectory . '/count';
        $total += self::$filesystem->exists($rootCountFile) ? (float)file_get_contents($rootCountFile) : 0;

        foreach ($finder as $directory) {
            $path = $directory->getPathname() . '/count';

            if(!self::$filesystem->exists($path)) {
                $randomNumber = random_int(1, 1000) / 100;
                $total += $randomNumber;

                self::$filesystem->touch($path);
                self::$filesystem->dumpFile($path, $randomNumber);

                $files[] = $path;
            }
            else {
                $total += (float)file_get_contents($path);
            }
        }

        return array($files, $total);
    }

    public function testCountTotalInRootDirectory() : void
    {
        [$files, $total] = $this->countTotal(self::ROOT_DIR);

        self::$commandTester->execute([]);

        self::$filesystem->remove($files);

        $output = self::$commandTester->getDisplay();
        $this->assertEquals('Total: ' . $total, trim($output));
    }

    public function testCountTotalInSpecifiedDirectory() : void
    {
        [$files, $total] = $this->countTotal(self::ROOT_DIR . '/src');

        self::$commandTester->execute([
            'directory' => 'src'
        ]);

        self::$filesystem->remove($files);

        $output = self::$commandTester->getDisplay();
        $this->assertEquals('Total: ' . $total, trim($output));
    }

}