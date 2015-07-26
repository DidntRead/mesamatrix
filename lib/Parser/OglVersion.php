<?php
/*
 * This file is part of mesamatrix.
 *
 * Copyright (C) 2014 Romain "Creak" Failliot.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mesamatrix\Parser;

class OglVersion
{
    public function __construct($glName, $glVersion, $glslName, $glslVersion, $hints) {
        $this->setGlName($glName);
        $this->setGlVersion($glVersion);
        $this->setGlslName($glslName);
        $this->setGlslVersion($glslVersion);
        $this->hints = $hints;
        $this->extensions = array();
    }

    // GL name
    public function setGlName($name) {
        $this->glName = $name;
    }
    public function getGlName() {
        return $this->glName;
    }

    // GL version
    public function setGlVersion($version) {
        $this->glVersion = $version;
    }
    public function getGlVersion() {
        return $this->glVersion;
    }

    // GLSL name
    public function setGlslName($name) {
        $this->glslName = $name;
    }
    public function getGlslName() {
        return $this->glslName;
    }

    // GLSL version
    public function setGlslVersion($version) {
        $this->glslVersion = $version;
    }
    public function getGlslVersion() {
        return $this->glslVersion;
    }

    /**
     * Add an extension, or merge it if it already exists.
     *
     * @param string $name Name of the extension.
     * @param string $status Status of the extension.
     * @param array $supportedDrivers List of drivers supported for this extension.
     * @param \Mesamatrix\Git\Commit $commit The commit used by the parser.
     *
     * @return OglExtension The new or existing extension.
     */
    public function addExtension($name, $status, $supportedDrivers = array(), $commit = null) {
        $newExtension = new OglExtension($name, $status, $this->hints, $supportedDrivers);
        return $this->addExtension2($newExtension, $commit);
    }

    public function addExtension2(OglExtension $extension, \Mesamatrix\Git\Commit $commit) {
        $retExt = null;
        $existingExt = $this->getExtensionByName($extension->getName());
        if ($existingExt !== null) {
            $existingExt->incorporate($extension, $commit);
            $retExt = $existingExt;
        }
        else {
            $this->extensions[] = $extension;
            $retExt = $extension;
        }

        return $retExt;
    }

    /**
     * Get the list of all extensions.
     *
     * @return OglExtension[] All the extensions.
     */
    public function getExtensions() {
        return $this->extensions;
    }

    /**
     * Find the extensions with the given name.
     *
     * @param string $name The name of the extension to find.
     *
     * @return OglExtension The extension or null if not found.
     */
    public function getExtensionByName($name) {
        foreach ($this->extensions as $extension) {
            if ($extension->getName() === $name) {
                return $extension;
            }
        }
        return null;
    }

    private $glName;
    private $glVersion;
    private $glslName;
    private $glslVersion;
    private $hints;
    private $extensions;
};
