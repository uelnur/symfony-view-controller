<?php

namespace Uelnur\SymfonyViewController;

interface ViewFirewallInterface {
    public function viewIsGranted(ViewContext $viewContext): ?bool;
    public function viewIfNotGranted(ViewContext $viewContext): void;
}
