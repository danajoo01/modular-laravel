<?php namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleCommand extends GeneratorCommand{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:module';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new module (folder structure)';

	protected $type = 'Module';
	/**
	 * The current stub.
	 *
	 * @var string
	 */
	protected $currentStub;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// check if module exists
	
		if(!$this->option('model') && !$this->option('controller') )
		{
			if($this->files->exists(app_path().'/Modules/'.$this->getNameInput())) 
			return $this->error($this->type.' already exists!');
			// Create Controller
			$this->generate('controller');
			
			// Create Model
			$this->generate('model');
			
			// Create Views folder
			$this->generate('view_bb_desktop');

			$this->generate('view_hb_desktop');
                        
                        $this->generate('view_sd_desktop');

			$this->generate('view_bb_mobile');

			$this->generate('view_hb_mobile');
                        
                        $this->generate('view_sd_mobile');
			
			//Flag for no translation
			if ( ! $this->option('no-translation')) // Create Translations folder
				$this->generate('translation');

			// Create Routes file
			$this->generate('routes');
			/*if ( ! $this->option('no-migration'))
			{
				$table = str_plural(snake_case(class_basename($this->argument('name'))));
				$this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
			}*/
		}

		if($this->option('model'))
		{
			// Create Model
			$this->generate('model');
		}

		if($this->option('controller'))
		{
			// Create Model
			$this->generate('controller');
		}

		$this->info($this->type.' created successfully.');
	}

	protected function generate($type)
	{
		switch ($type) {
			case 'controller':
				$filename = ($this->option('controller')) ? studly_case(class_basename($this->option('controller'))) : studly_case(class_basename($this->getNameInput()).ucfirst($type));
				$folder = ucfirst($type).'s\\';
				$stubs_file = 'controller';
				break;
			case 'model':
				$filename = ($this->option('model')) ? studly_case(class_basename($this->option('model'))) : studly_case(class_basename($this->getNameInput()));
				$folder = ucfirst($type).'s\\';
				$stubs_file = 'model';
				break;
			case 'view_bb_desktop':
				$stubs_file = 'view';
				$filename = 'index.blade';
				$folder = ucfirst($stubs_file).'s\\berrybenka\\desktop\\'. $this->getNameInput().'\\';
				break;
			case 'view_hb_desktop':
				$stubs_file = 'view';
				$filename = 'index.blade';
				$folder = ucfirst($stubs_file).'s\\hijabenka\\desktop\\'. $this->getNameInput().'\\';
				break;
                        case 'view_sd_desktop':
				$stubs_file = 'view';
				$filename = 'index.blade';
				$folder = ucfirst($stubs_file).'s\\shopdeca\\desktop\\'. $this->getNameInput().'\\';
				break;    
			case 'view_bb_mobile':
				$stubs_file = 'view';
				$filename = 'index.blade';
				$folder = ucfirst($stubs_file).'s\\berrybenka\\mobile\\'. $this->getNameInput().'\\';
				break;
			case 'view_hb_mobile':
				$stubs_file = 'view';
				$filename = 'index.blade';
				$folder = ucfirst($stubs_file).'s\\hijabenka\\mobile\\'. $this->getNameInput().'\\';
				break;
                        case 'view_sd_mobile':
				$stubs_file = 'view';
				$filename = 'index.blade';
				$folder = ucfirst($stubs_file).'s\\shopdeca\\mobile\\'. $this->getNameInput().'\\';
				break;
			case 'translation':
				$filename = 'example';
				$folder = ucfirst($type).'s\\en\\';
				$stubs_file = 'translation';
				break;
			
			case 'routes':
				$filename = 'routes';
				$folder = '';
				$stubs_file = 'routes';
				break;
		}
	
		$name = $this->parseName('Modules\\'.$this->getNameInput().'\\'.$folder.$filename);

		if(!$this->files->exists($path = $this->getPath($name)))
		{
			$this->currentStub = __DIR__.'/stubs/'.$stubs_file.'.stub';
			$this->makeDirectory($path);
		}
		else
		{
			return $this->error($this->type.' already exists!');
		}
		
		$this->files->put($path, $this->buildClass($name));
	}

	/**
	 * Get the full namespace name for a given class.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function getNamespace($name)
	{
		return trim(implode('\\', array_map('ucfirst', array_slice(explode('\\', $name), 0, -1))), '\\');
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function buildClass($name)
	{
		$stub = $this->files->get($this->getStub());

		$action_name = $name;

		if($this->option('model'))
		{
			$action_name = $this->option('model');
		}

		if($this->option('controller'))
		{
			$action_name = $this->option('controller');
		}

		$build = $this->replaceName($stub, $this->getNameInput())->replaceNamespace($stub, $name)->replaceClass($stub, $name);
		return $build;
	}

	/**
	 * Replace the name for the given stub.
	 *
	 * @param  string  $stub
	 * @param  string  $name
	 * @return string
	 */
	protected function replaceName(&$stub, $name)
	{
		$stub = str_replace('DummyTitle', $name, $stub);
		$stub = str_replace('DummyUCtitle', ucfirst($name), $stub);
		return $this;
	}

	/**
	 * Replace the class name for the given stub.
	 *
	 * @param  string  $stub
	 * @param  string  $name
	 * @return string
	 */
	protected function replaceClass($stub, $name)
	{
		$class = str_ireplace($this->getNamespace($name).'\\', '', $name);
		return str_replace('DummyClass', $class, $stub);
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->currentStub;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['name', InputArgument::REQUIRED, 'Module name.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['model', null, InputOption::VALUE_REQUIRED, 'Create model files.'],
			['controller', null, InputOption::VALUE_REQUIRED, 'Create controller files.'],

			['no-migration', null, InputOption::VALUE_NONE, 'Do not create new migration files.'],
			['no-translation', null, InputOption::VALUE_NONE, 'Do not create module translation filesystem.'],
		];
	}

}
