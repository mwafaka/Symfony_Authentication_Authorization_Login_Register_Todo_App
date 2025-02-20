# Symfony Todo App

This is a simple Todo application built using Symfony 6. The app allows users to create, toggle, and delete todos with a Bootstrap-styled UI for a better user experience.

## Features
- Create a new Todo
- Mark a Todo as completed/uncompleted
- Delete a Todo
- Responsive design using Bootstrap

## Installation and Setup

### 1. Install Symfony CLI (if not installed)
Ensure Symfony CLI is installed globally:
```sh
curl -sS https://get.symfony.com/cli | bash
mv ~/.symfony/bin/symfony /usr/local/bin/symfony
```

### 2. Create a New Symfony Project
```sh
symfony new todo-app --webapp
cd todo-app
```

### 3. Install Required Dependencies
```sh
composer require symfony/orm-pack
composer require --dev symfony/maker-bundle
composer require symfony/twig-bundle
composer require symfony/form symfony/validator symfony/security-bundle
```

### 4. Configure Database
Update your `.env` file with your database credentials:
```env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/todo_db?serverVersion=8.0"
```
Run the following command to create the database:
```sh
php bin/console doctrine:database:create
```

### 5. Create the Todo Entity
```sh
php bin/console make:entity Todo
```
Define properties:
```php
#[ORM\Entity]
class Todo {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $title = null;
    
    #[ORM\Column]
    private ?bool $completed = false;
}
```
Run migrations:
```sh
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### 6. Create the Todo Controller
```sh
php bin/console make:controller TodoController
```
Update the controller:
```php
#[Route('/todo', name: 'todo_')]
class TodoController extends AbstractController {
    #[Route('/', name: 'index')]
    public function index(TodoRepository $todoRepository): Response {
        return $this->render('todo/index.html.twig', [
            'todos' => $todoRepository->findAll(),
        ]);
    }
    
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($todo);
            $entityManager->flush();
            return $this->redirectToRoute('todo_index');
        }
        return $this->render('todo/create.html.twig', ['form' => $form->createView()]);
    }
}
```

### 7. Create Form for Adding Todos
```sh
php bin/console make:form TodoType Todo
```
Modify the form class:
```php
class TodoType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('title', TextType::class, ['label' => 'Todo Title'])
            ->add('completed', CheckboxType::class, [
                'label' => 'Completed',
                'required' => false,
            ]);
    }
}
```

### 8. Run the Application
```sh
symfony server:start --no-tls
```
Visit `http://127.0.0.1:8000` in your browser.

## Optional Enhancements
- Add authentication using Symfony Security
- Improve UI with additional Bootstrap components
- Use JavaScript for interactive actions

## License
This project is open-source and available for use under the MIT License.

---
This guide provides all necessary steps to set up and run the Todo app using Symfony 6. Let me know if you need any modifications!

