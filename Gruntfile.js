module.exports = function(grunt) {
    /**
     * Configure individual grunt tasks
    **/
    grunt.config.init(
        {
            composer: {
                options: {
                    usePhp: true,
                    cwd: ".",
                    composerLocation: "/usr/local/bin/composer"
                },
                autoload: {
                    options: {
                        usePhp: true,
                        cwd: ".",
                        composerLocation: "/usr/local/bin/composer"
                    }
                }
            },
            exec: {
                phpstan: "vendor/bin/phpstan analyse -l 7 " +
                    "-c phpstan.neon --memory-limit=4G " +
                    "src/php"
            },
            phpcs: {
                application: {
                    src: [
                        "src/**/*.php"
                    ]
                },
                options: {
                    bin: "vendor/bin/phpcs",
                    standard: "Zend"
                }
            },
            phpcsfixer: {
                test: {
                    app: {
                        // Handled in config (src/.php_cs.dist)
                        dir: []
                    },
                    options: {
                        bin: 'vendor/bin/php-cs-fixer',
                        usingCache: "no",
                        dryRun: true,
                        configfile: 'src/.php_cs.dist',
                        verbose: true,
                        diff: true,
                        diffFormat: 'udiff'
                    }
                }
            },
            "phpunit-runner": {
                options: {
                    colours: true,
                    phpunit: "vendor/bin/phpunit",
                    processIsolation: true,
                    reportUselessTests: true,
                    showUncoveredFiles: true,
                    strictCoverage: true,
                    verbose: true,
                    logJunit: "reports/unit.xml",
                    testdoxHtml: "reports/testdox.html"
                },
                unit: {
                    options: {
                        configuration: "test/unit/coverage.xml"
                    },
                    files: {
                        testFolder: "test/unit"
                    }
                },
                functional: {
                    options: {
                        configuration: "test/functional/coverage.xml"
                    },
                    files: {
                        testFolder: "test/functional"
                    }
                }
            }
        }
    );

    /**
     * Register invokeable grunt tasks
    **/
    grunt.registerTask(
        "makeClassmaps",
        [
            "composer:autoload:dump-autoload"
        ]
    );

    grunt.registerTask(
        "test",
        [
            "phpcs",
            "phpcsfixer",
            "exec:phpstan",
            "phpunit-runner:unit"
        ]
    );

    grunt.registerTask(
        "phpcs",
        [
            "phpcs",
            "phpcsfixer"
        ]
    );

    grunt.registerTask(
        "phpstan",
        [
            "exec:phpstan"
        ]
    );

    grunt.registerTask(
        "phpunit",
        [
            "phpunit-runner:unit",
            "phpunit-runner:functional"
        ]
    );

    grunt.registerTask(
        "phpunit-unit",
        [
            "phpunit-runner:unit"
        ]
    );

    grunt.registerTask(
        "phpunit-functional",
        [
            "phpunit-runner:functional"
        ]
    );

    /**
     * Load NPM dependencies s.t. we can actually invoke tasks
    **/
    require('matchdep').filterDev(
        [
            'grunt-*',
            '!grunt-template-jasmine-*',
            '!grunt-aws'
        ]
    ).forEach(grunt.loadNpmTasks);
}
